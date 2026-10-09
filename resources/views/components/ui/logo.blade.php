@props(['mark' => false])
<span {{ $attributes->class('inline-flex items-center gap-2') }}>
    <svg viewBox="0 0 32 32" class="size-8 shrink-0" aria-hidden="true">
        <defs>
            <linearGradient id="qrx-logo-gradient" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="var(--color-brand-500)" />
                <stop offset="1" stop-color="var(--color-accent-600)" />
            </linearGradient>
        </defs>
        <rect width="32" height="32" rx="8" fill="url(#qrx-logo-gradient)" />
        <rect x="7" y="7" width="7" height="7" rx="1.5" fill="none" stroke="#fff" stroke-width="2" />
        <rect x="18" y="7" width="7" height="7" rx="1.5" fill="none" stroke="#fff" stroke-width="2" />
        <rect x="7" y="18" width="7" height="7" rx="1.5" fill="none" stroke="#fff" stroke-width="2" />
        <rect x="18.5" y="18.5" width="2.5" height="2.5" rx="0.6" fill="#fff" />
        <rect x="22.5" y="22.5" width="2.5" height="2.5" rx="0.6" fill="#fff" />
        <rect x="22.5" y="18.5" width="2.5" height="2.5" rx="0.6" fill="#fff" fill-opacity="0.6" />
    </svg>
    @unless ($mark)
        <span class="text-lg font-semibold tracking-tight text-ink" dir="ltr">{{ config('app.name') }}</span>
    @endunless
</span>
