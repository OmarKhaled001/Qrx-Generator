@props(['text', 'label' => null])
<button
    type="button"
    x-data="copyable(@js($text))"
    x-on:click="copy"
    {{ $attributes->class('inline-flex h-9 items-center gap-1.5 rounded-md border border-line-strong bg-surface px-3 text-sm font-medium text-ink transition hover:bg-sunken') }}
>
    <x-heroicon-o-clipboard-document x-show="! copied" class="size-4" aria-hidden="true" />
    <x-heroicon-o-check x-show="copied" x-cloak class="size-4 text-success-600" aria-hidden="true" />
    <span x-show="! copied">{{ $label ?? __('Copy') }}</span>
    <span x-show="copied" x-cloak role="status">{{ __('Copied') }}</span>
</button>
