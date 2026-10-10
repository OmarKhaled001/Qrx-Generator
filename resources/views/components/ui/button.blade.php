@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'icon' => null, 'type' => 'button'])
@php
    // Icons that point somewhere (arrows, send) face the reading direction.
    $iconClass = 'size-4 shrink-0'.($icon && preg_match('/arrow-(left|right|long-left|long-right|top-right|uturn)|chevron-(left|right)|paper-airplane/', $icon) ? ' rtl:-scale-x-100' : '');
    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-md font-semibold whitespace-nowrap select-none',
        'transition-[background-color,border-color,color,box-shadow,transform] duration-150 ease-out active:scale-[0.97]',
        'disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50',
        match ($variant) {
            'secondary' => 'border border-line-strong bg-surface text-ink shadow-xs hover:border-ink-subtle/50 hover:bg-sunken',
            'ghost' => 'text-ink-muted hover:bg-sunken hover:text-ink',
            'danger' => 'bg-danger-600 text-white shadow-xs hover:bg-danger-700',
            'brand' => 'bg-brand-gradient text-white shadow-glow hover:brightness-110',
            default => 'bg-primary text-on-primary shadow-xs hover:bg-primary-hover hover:shadow-glow',
        },
        match ($size) {
            'sm' => 'h-8 px-3 text-sm',
            'lg' => 'h-12 px-6 text-base',
            'icon' => 'size-9',
            default => 'h-10 px-4 text-sm',
        },
    ]);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-dynamic-component :component="$icon" :class="$iconClass" aria-hidden="true" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-dynamic-component :component="$icon" :class="$iconClass" aria-hidden="true" />@endif
        {{ $slot }}
    </button>
@endif
