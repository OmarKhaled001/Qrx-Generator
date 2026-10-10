{{-- Section heading on the landing page: small eyebrow, title, optional lead. --}}
@props(['eyebrow' => null, 'title', 'description' => null, 'center' => false])
<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $center]) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold text-primary-ink">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ $title }}</h2>
    @if ($description)
        <p class="mt-4 text-lg text-ink-muted">{{ $description }}</p>
    @endif
</div>
