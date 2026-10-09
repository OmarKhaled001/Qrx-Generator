@props(['icon' => 'heroicon-o-qr-code', 'heading', 'description' => null])
<div {{ $attributes->class('flex flex-col items-center rounded-lg border border-dashed border-line-strong bg-surface px-6 py-14 text-center') }}>
    <div class="grid size-12 place-items-center rounded-full bg-primary-soft text-primary-ink">
        <x-dynamic-component :component="$icon" class="size-6" aria-hidden="true" />
    </div>
    <h3 class="mt-4 text-base font-semibold text-ink">{{ $heading }}</h3>
    @if ($description)<p class="mt-1 max-w-sm text-sm text-ink-muted">{{ $description }}</p>@endif
    @if ($slot->isNotEmpty())<div class="mt-6">{{ $slot }}</div>@endif
</div>
