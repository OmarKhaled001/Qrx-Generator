@props(['label', 'value', 'hint' => null, 'icon' => null])
<div {{ $attributes->class('flex flex-col rounded-lg border border-line bg-surface p-5 shadow-card inset-shadow-highlight sm:p-6') }}>
    <div class="flex items-center justify-between gap-3">
        <p class="text-sm font-medium text-ink-muted">{{ $label }}</p>
        @if ($icon)
            <span class="grid size-9 place-items-center rounded-md bg-primary-soft text-primary-ink">
                <x-dynamic-component :component="$icon" class="size-5" aria-hidden="true" />
            </span>
        @endif
    </div>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-ink tabular-nums">{{ $value }}</p>
    @if ($hint)<p class="mt-1 text-sm text-ink-subtle">{{ $hint }}</p>@endif
    {{ $slot }}
</div>
