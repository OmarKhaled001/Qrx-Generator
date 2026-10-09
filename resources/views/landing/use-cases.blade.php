{{-- Use cases by industry: accessible tabs (arrow keys move between tabs). --}}
@php
    $cases = [
        'restaurants' => ['icon' => 'heroicon-o-cake', 'label' => __('Restaurants and cafés'), 'preset' => 'forest', 'sample' => __('Table menu'),
            'text' => __('Put your menu on every table and change prices or dishes without reprinting. Guests join your Wi-Fi with one scan.'),
            'types' => [\App\Enums\QrType::Url, \App\Enums\QrType::Wifi]],
        'retail' => ['icon' => 'heroicon-o-shopping-bag', 'label' => __('Shops and retail'), 'preset' => 'sunset', 'sample' => __('Seasonal offer'),
            'text' => __('Link packaging and shelf tags to product pages, offers or your shop on the map. Swap the offer when the season changes.'),
            'types' => [\App\Enums\QrType::Url, \App\Enums\QrType::Location]],
        'events' => ['icon' => 'heroicon-o-ticket', 'label' => __('Events and conferences'), 'preset' => 'violet', 'sample' => __('Speaker badge'),
            'text' => __('Badges that save a speaker’s contact card, posters that open the agenda and signs that start a registration text.'),
            'types' => [\App\Enums\QrType::Vcard, \App\Enums\QrType::Url, \App\Enums\QrType::Sms]],
        'real-estate' => ['icon' => 'heroicon-o-home-modern', 'label' => __('Real estate'), 'preset' => 'midnight', 'sample' => __('For-sale sign'),
            'text' => __('Signs that open the listing or call the agent. When the property sells, point the same sign at the next one.'),
            'types' => [\App\Enums\QrType::Url, \App\Enums\QrType::Phone, \App\Enums\QrType::Location]],
        'services' => ['icon' => 'heroicon-o-building-storefront', 'label' => __('Clinics and services'), 'preset' => 'ocean', 'sample' => __('Booking flyer'),
            'text' => __('Let people book by phone, find your door on the map or email you straight from a flyer.'),
            'types' => [\App\Enums\QrType::Phone, \App\Enums\QrType::Location, \App\Enums\QrType::Email]],
    ];
@endphp
<section id="use-cases" class="border-y border-line bg-surface">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28" x-data="{ tab: '{{ array_key_first($cases) }}' }">
        <x-landing.heading :eyebrow="__('Use cases')" :title="__('Made for the places people scan')" :description="__('A few ways businesses use dynamic codes. Every one of them can be changed after printing.')" />

        <div class="-mx-4 mt-10 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0">
            <div class="flex w-max gap-2" role="tablist" aria-label="{{ __('Use cases') }}"
                x-on:keydown.right.prevent="document.dir === 'rtl' ? $focus.within($el).wrap().prev() : $focus.within($el).wrap().next()"
                x-on:keydown.left.prevent="document.dir === 'rtl' ? $focus.within($el).wrap().next() : $focus.within($el).wrap().prev()">
                @foreach ($cases as $key => $case)
                    <button type="button" role="tab" id="use-case-tab-{{ $key }}" aria-controls="use-case-{{ $key }}"
                        :aria-selected="tab === '{{ $key }}'" :tabindex="tab === '{{ $key }}' ? 0 : -1"
                        x-on:click="tab = '{{ $key }}'" x-on:focus="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-primary bg-primary-soft text-primary-ink' : 'border-line text-ink-muted hover:bg-sunken hover:text-ink'"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors">
                        <x-dynamic-component :component="$case['icon']" class="size-4" aria-hidden="true" />
                        {{ $case['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        @foreach ($cases as $key => $case)
            <div id="use-case-{{ $key }}" role="tabpanel" aria-labelledby="use-case-tab-{{ $key }}" tabindex="0"
                x-show="tab === '{{ $key }}'" @if (! $loop->first) x-cloak @endif
                x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-2"
                class="mt-8 grid items-center gap-10 rounded-2xl border border-line bg-page p-6 sm:p-10 lg:grid-cols-[1.2fr_1fr]">
                <div>
                    <h3 class="text-2xl font-bold tracking-tight text-ink">{{ $case['label'] }}</h3>
                    <p class="mt-3 text-lg text-ink-muted">{{ $case['text'] }}</p>
                    <ul class="mt-6 flex flex-wrap gap-2" aria-label="{{ __('QR code types') }}">
                        @foreach ($case['types'] as $type)
                            <li class="inline-flex items-center gap-2 rounded-full bg-surface px-3 py-1.5 text-sm font-medium text-ink shadow-xs ring-1 ring-line">
                                <x-dynamic-component :component="$type->icon()" class="size-4 text-primary-ink" aria-hidden="true" />
                                {{ $type->label() }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <figure class="mx-auto w-full max-w-64 rotate-2 rounded-2xl border border-line bg-surface p-5 shadow-raised transition-transform duration-500 hover:rotate-0 rtl:-rotate-2 rtl:hover:rotate-0">
                    <img src="{{ $samples[$case['preset']] }}" alt="" class="aspect-square w-full" loading="lazy">
                    <figcaption class="mt-3 flex items-center justify-between gap-2 text-sm">
                        <span class="font-semibold text-ink">{{ $case['sample'] }}</span>
                        <x-ui.badge color="success" dot>{{ __('Active') }}</x-ui.badge>
                    </figcaption>
                </figure>
            </div>
        @endforeach
    </div>
</section>
