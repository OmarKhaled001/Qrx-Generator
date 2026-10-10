@props(['heading' => null, 'description' => null, 'padded' => true, 'icon' => null])
<section {{ $attributes->class('rounded-lg border border-line bg-surface shadow-card inset-shadow-highlight') }}>
    @if ($heading || isset($actions))
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
            <div class="flex min-w-0 items-start gap-3">
                @if ($icon)
                    <span class="grid size-8 shrink-0 place-items-center rounded-md bg-primary-soft text-primary-ink">
                        <x-dynamic-component :component="$icon" class="size-4" aria-hidden="true" />
                    </span>
                @endif
                <div class="min-w-0">
                    @if ($heading)<h2 class="text-base font-semibold text-ink">{{ $heading }}</h2>@endif
                    @if ($description)<p class="mt-0.5 text-sm text-ink-muted">{{ $description }}</p>@endif
                </div>
            </div>
            @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div @class(['p-5 sm:p-6' => $padded])>
        {{ $slot }}
    </div>
</section>
