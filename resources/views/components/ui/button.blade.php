@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'icon' => null, 'type' => 'button'])
@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-md font-medium whitespace-nowrap transition',
        'disabled:pointer-events-none disabled:opacity-50',
        match ($variant) {
            'secondary' => 'border border-line-strong bg-surface text-ink hover:bg-sunken',
            'ghost' => 'text-ink-muted hover:bg-sunken hover:text-ink',
            'danger' => 'bg-danger-600 text-white shadow-card hover:bg-danger-700',
            default => 'bg-primary text-on-primary shadow-card hover:bg-primary-hover',
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
        @if ($icon)<x-dynamic-component :component="$icon" class="size-4 shrink-0" aria-hidden="true" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-dynamic-component :component="$icon" class="size-4 shrink-0" aria-hidden="true" />@endif
        {{ $slot }}
    </button>
@endif
