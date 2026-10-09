@props(['heading' => null, 'description' => null, 'padded' => true])
<section {{ $attributes->class('rounded-lg border border-line bg-surface shadow-card') }}>
    @if ($heading || isset($actions))
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-line px-5 py-4">
            <div class="min-w-0">
                @if ($heading)<h2 class="text-base font-semibold text-ink">{{ $heading }}</h2>@endif
                @if ($description)<p class="mt-0.5 text-sm text-ink-muted">{{ $description }}</p>@endif
            </div>
            @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div @class(['p-5' => $padded])>
        {{ $slot }}
    </div>
</section>
