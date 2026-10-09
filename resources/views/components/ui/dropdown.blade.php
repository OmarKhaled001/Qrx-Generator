@props(['align' => 'end', 'width' => 'w-56'])
<div x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape="open = false" {{ $attributes->class('relative') }}>
    <div x-on:click="open = ! open">{{ $trigger }}</div>
    <div
        x-show="open"
        x-cloak
        x-transition.origin.top
        @class([
            "absolute z-40 mt-2 {$width} overflow-hidden rounded-md border border-line bg-raised py-1 shadow-pop",
            'end-0' => $align === 'end',
            'start-0' => $align === 'start',
        ])
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
