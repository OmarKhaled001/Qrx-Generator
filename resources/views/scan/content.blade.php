{{-- What a scanner sees for each type. All link targets are built from encoded or allowlisted values. --}}
@php
    $value = fn (string $key) => trim((string) ($payload[$key] ?? ''));
    $tel = fn (string $number) => 'tel:'.preg_replace('/[^0-9+]/', '', $number);
    $actionClass = 'flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 text-base font-semibold text-on-primary shadow-glow transition-[background-color,transform] hover:bg-primary-hover active:scale-[0.98]';
@endphp

@switch($type)
    @case('url')
        {{-- Reached only when the stored URL is not http(s): show it, never link it. --}}
        <x-ui.alert type="warning">{{ __('This link cannot be opened safely.') }}</x-ui.alert>
        <p class="mt-4 rounded-md bg-sunken p-3 font-mono text-sm break-all text-ink" dir="ltr">{{ $value('url') }}</p>
        @break

    @case('text')
        <p class="text-base leading-relaxed whitespace-pre-line text-ink" dir="auto">{{ $value('text') }}</p>
        <x-ui.copy-button :text="$value('text')" class="mt-5" />
        @break

    @case('phone')
        <p class="text-center text-2xl font-semibold tracking-wide text-ink tabular-nums" dir="ltr">{{ $value('phone') }}</p>
        <a href="{{ $tel($value('phone')) }}" class="{{ $actionClass }} mt-5">
            <x-heroicon-o-phone class="size-5" aria-hidden="true" />{{ __('Call now') }}
        </a>
        @break

    @case('sms')
        <p class="text-center text-xl font-semibold text-ink tabular-nums" dir="ltr">{{ $value('phone') }}</p>
        @if ($value('message') !== '')
            <p class="mt-4 rounded-md bg-sunken p-3 text-sm whitespace-pre-line text-ink" dir="auto">{{ $value('message') }}</p>
        @endif
        <a href="sms:{{ preg_replace('/[^0-9+]/', '', $value('phone')) }}?body={{ rawurlencode($value('message')) }}" class="{{ $actionClass }} mt-5">
            <x-heroicon-o-chat-bubble-left-ellipsis class="size-5" aria-hidden="true" />{{ __('Send message') }}
        </a>
        @break

    @case('email')
        <dl class="space-y-3 text-sm">
            <div><dt class="text-ink-subtle">{{ __('To') }}</dt><dd class="font-medium break-all text-ink" dir="ltr">{{ $value('email') }}</dd></div>
            @if ($value('subject') !== '')<div><dt class="text-ink-subtle">{{ __('Subject') }}</dt><dd class="text-ink" dir="auto">{{ $value('subject') }}</dd></div>@endif
            @if ($value('body') !== '')<div><dt class="text-ink-subtle">{{ __('Body') }}</dt><dd class="whitespace-pre-line text-ink" dir="auto">{{ $value('body') }}</dd></div>@endif
        </dl>
        <a href="mailto:{{ str_replace('%40', '@', rawurlencode($value('email'))) }}?subject={{ rawurlencode($value('subject')) }}&amp;body={{ rawurlencode($value('body')) }}" class="{{ $actionClass }} mt-5">
            <x-heroicon-o-envelope class="size-5" aria-hidden="true" />{{ __('Write email') }}
        </a>
        @break

    @case('vcard')
        <div class="text-center">
            <span class="mx-auto grid size-16 place-items-center rounded-full bg-primary-soft text-2xl font-semibold text-primary-ink" aria-hidden="true">{{ mb_strtoupper(mb_substr($value('full_name'), 0, 1)) }}</span>
            <p class="mt-3 text-xl font-semibold text-ink" dir="auto">{{ $value('full_name') }}</p>
            @if ($value('title') !== '' || $value('organization') !== '')
                <p class="text-sm text-ink-muted" dir="auto">{{ collect([$value('title'), $value('organization')])->filter()->implode(' · ') }}</p>
            @endif
        </div>
        <a href="{{ route('scan.vcard', $qrCode->code) }}" class="{{ $actionClass }} mt-6">
            <x-heroicon-o-user-plus class="size-5" aria-hidden="true" />{{ __('Save contact') }}
        </a>
        <ul class="mt-5 divide-y divide-line rounded-md border border-line text-sm">
            @foreach (['phone' => 'heroicon-o-device-phone-mobile', 'tel' => 'heroicon-o-phone'] as $field => $icon)
                @if ($value($field) !== '')
                    <li><a href="{{ $tel($value($field)) }}" class="flex items-center gap-3 px-4 py-3 text-ink hover:bg-sunken"><x-dynamic-component :component="$icon" class="size-5 text-ink-subtle" aria-hidden="true" /><span dir="ltr">{{ $value($field) }}</span></a></li>
                @endif
            @endforeach
            @if ($value('email') !== '')
                <li><a href="mailto:{{ str_replace('%40', '@', rawurlencode($value('email'))) }}" class="flex items-center gap-3 px-4 py-3 text-ink hover:bg-sunken"><x-heroicon-o-envelope class="size-5 text-ink-subtle" aria-hidden="true" /><span dir="ltr" class="break-all">{{ $value('email') }}</span></a></li>
            @endif
            @if (\App\Http\Controllers\ScanController::isSafeUrl($value('website')))
                <li><a href="{{ $value('website') }}" rel="noopener nofollow" class="flex items-center gap-3 px-4 py-3 text-ink hover:bg-sunken"><x-heroicon-o-globe-alt class="size-5 text-ink-subtle" aria-hidden="true" /><span dir="ltr" class="break-all">{{ $value('website') }}</span></a></li>
            @endif
            @if ($value('address') !== '')
                <li class="flex items-center gap-3 px-4 py-3 text-ink"><x-heroicon-o-map-pin class="size-5 shrink-0 text-ink-subtle" aria-hidden="true" /><span dir="auto">{{ $value('address') }}</span></li>
            @endif
        </ul>
        @if ($value('note') !== '')
            <p class="mt-4 text-sm whitespace-pre-line text-ink-muted" dir="auto">{{ $value('note') }}</p>
        @endif
        @break

    @case('wifi')
        <dl class="space-y-4">
            <div>
                <dt class="text-sm text-ink-subtle">{{ __('Network name') }}</dt>
                <dd class="mt-1 flex items-center justify-between gap-3"><span class="text-lg font-semibold break-all text-ink" dir="auto">{{ $value('ssid') }}</span><x-ui.copy-button :text="$value('ssid')" /></dd>
            </div>
            @if (($payload['encryption'] ?? 'WPA') !== 'nopass')
                <div x-data="{ shown: false }">
                    <dt class="text-sm text-ink-subtle">{{ __('Password') }}</dt>
                    <dd class="mt-1 flex items-center justify-between gap-3">
                        <span class="font-mono text-lg break-all text-ink" dir="ltr" x-text="shown ? @js($value('password')) : '••••••••'">••••••••</span>
                        <span class="flex shrink-0 gap-2">
                            <button type="button" class="inline-flex h-9 items-center rounded-md border border-line-strong px-3 text-sm text-ink hover:bg-sunken" x-on:click="shown = ! shown" x-text="shown ? @js(__('Hide')) : @js(__('Show'))">{{ __('Show') }}</button>
                            <x-ui.copy-button :text="$value('password')" />
                        </span>
                    </dd>
                </div>
            @endif
            <div>
                <dt class="text-sm text-ink-subtle">{{ __('Security') }}</dt>
                <dd class="mt-1 text-ink">{{ ($payload['encryption'] ?? 'WPA') === 'nopass' ? __('Open network (no password)') : ($payload['encryption'] ?? 'WPA') }}</dd>
            </div>
        </dl>
        <x-ui.alert type="info" class="mt-5">{{ __('Open your Wi-Fi settings, pick this network and paste the password.') }}</x-ui.alert>
        @break

    @case('location')
        @php
            $lat = (float) ($payload['latitude'] ?? 0);
            $lng = (float) ($payload['longitude'] ?? 0);
            $coordinates = number_format($lat, 6, '.', '').','.number_format($lng, 6, '.', '');
        @endphp
        @if ($value('label') !== '')
            <p class="text-lg font-semibold text-ink" dir="auto">{{ $value('label') }}</p>
        @endif
        <p class="mt-1 font-mono text-sm text-ink-muted" dir="ltr">{{ $coordinates }}</p>
        <div class="mt-5 grid gap-3">
            <a href="https://www.google.com/maps/search/?api=1&amp;query={{ $coordinates }}" rel="noopener nofollow" class="{{ $actionClass }}">
                <x-heroicon-o-map class="size-5" aria-hidden="true" />{{ __('Open in Google Maps') }}
            </a>
            <a href="https://maps.apple.com/?ll={{ $coordinates }}&amp;q={{ rawurlencode($value('label') ?: $coordinates) }}" rel="noopener nofollow" class="flex h-12 w-full items-center justify-center gap-2 rounded-md border border-line-strong px-4 text-base font-medium text-ink transition hover:bg-sunken">
                {{ __('Open in Apple Maps') }}
            </a>
        </div>
        @break
@endswitch
