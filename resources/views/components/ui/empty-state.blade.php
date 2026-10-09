@props(['icon' => 'heroicon-o-qr-code', 'heading', 'description' => null])
<div {{ $attributes->class('flex flex-col items-center rounded-lg border border-dashed border-line-strong bg-surface bg-glow px-6 py-16 text-center') }}>
    <div class="grid size-14 place-items-center rounded-2xl bg-primary-soft text-primary-ink ring-8 ring-primary-soft/40">
        <x-dynamic-component :component="$icon" class="size-7" aria-hidden="true" />
    </div>
    <h3 class="mt-5 text-lg font-semibold text-ink">{{ $heading }}</h3>
    @if ($description)<p class="mt-1 max-w-sm text-sm text-ink-muted">{{ $description }}</p>@endif
    @if ($slot->isNotEmpty())<div class="mt-6">{{ $slot }}</div>@endif
</div>
