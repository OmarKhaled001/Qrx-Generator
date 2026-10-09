{{-- Open with $dispatch('open-modal', 'name'); closes on Escape, backdrop click or 'close-modal'. --}}
@props(['name', 'heading', 'maxWidth' => 'max-w-md'])
<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if (! $event.detail || $event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
>
    <div class="absolute inset-0 bg-neutral-950/50 backdrop-blur-[2px]" x-on:click="open = false" x-show="open" x-transition.opacity></div>
    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-{{ $name }}-title"
        x-show="open"
        x-trap.noscroll="open"
        x-transition
        {{ $attributes->class("relative w-full {$maxWidth} rounded-lg border border-line bg-raised p-6 shadow-pop") }}
    >
        <h2 id="modal-{{ $name }}-title" class="text-lg font-semibold text-ink">{{ $heading }}</h2>
        <div class="mt-3 text-sm text-ink-muted">{{ $slot }}</div>
        @isset($footer)
            <div class="mt-6 flex flex-wrap justify-end gap-2">{{ $footer }}</div>
        @endisset
    </div>
</div>
