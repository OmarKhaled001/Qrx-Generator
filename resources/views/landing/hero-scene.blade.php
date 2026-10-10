{{--
    Hero illustration: a printed card, a phone that scans it, and the code turning into content.
    The markup is the finished frame (what no-JS and reduced-motion visitors see); resources/js/hero.js
    animates from the first frame to this one. No <text> inside: the whole drawing mirrors in RTL.
--}}
@php
    $qr = \App\Support\QrMatrix::for(url('/'));
    $posterScale = 168 / $qr['size'];
    $finderScale = 104 / $qr['size'];
    $rows = [[220, 'var(--color-brand-500)'], [276, 'var(--color-accent-500)'], [332, 'var(--color-brand-400)']];
@endphp
<div data-hero-scene class="relative mx-auto w-full max-w-xl rtl:-scale-x-100">
    <svg viewBox="0 0 600 560" class="h-auto w-full overflow-visible" aria-hidden="true" focusable="false">
        <defs>
            <radialGradient id="hero-glow" cx="0.5" cy="0.5" r="0.5">
                <stop offset="0" stop-color="var(--color-brand-300)" stop-opacity="0.45" />
                <stop offset="1" stop-color="var(--color-brand-300)" stop-opacity="0" />
            </radialGradient>
            <linearGradient id="hero-brand" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="var(--color-brand-500)" />
                <stop offset="1" stop-color="var(--color-accent-600)" />
            </linearGradient>
            <linearGradient id="hero-beam" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="var(--color-brand-400)" stop-opacity="0" />
                <stop offset="1" stop-color="var(--color-brand-400)" stop-opacity="0.55" />
            </linearGradient>
            <clipPath id="hero-screen">
                <rect x="332" y="68" width="200" height="424" rx="28" />
            </clipPath>
        </defs>

        <circle cx="330" cy="290" r="250" fill="url(#hero-glow)" />

        {{-- Printed card with the code. --}}
        <g data-part="poster">
            <rect x="46" y="170" width="232" height="316" rx="22" fill="var(--color-neutral-950)" opacity="0.07" />
            <rect x="40" y="160" width="232" height="316" rx="22" fill="var(--surface)" stroke="var(--line)" stroke-width="1.5" />
            <rect x="68" y="188" width="110" height="12" rx="6" fill="var(--ink)" opacity="0.85" />
            <rect x="68" y="210" width="150" height="8" rx="4" fill="var(--ink-subtle)" opacity="0.5" />
            <rect x="60" y="232" width="192" height="192" rx="14" fill="#ffffff" stroke="var(--line)" />
            <path d="{{ $qr['path'] }}" fill="#121825" transform="translate(72 244) scale({{ $posterScale }})" />
            <rect x="68" y="442" width="120" height="10" rx="5" fill="var(--color-brand-600)" opacity="0.85" />
            <circle data-part="ping" cx="156" cy="328" r="104" fill="none" stroke="var(--color-brand-400)" stroke-width="3" opacity="0" />
        </g>

        {{-- Phone. --}}
        <ellipse cx="432" cy="526" rx="112" ry="12" fill="var(--color-neutral-950)" opacity="0.12" />
        <g data-part="phone">
            <rect x="320" y="56" width="224" height="448" rx="38" fill="#121825" />
            <rect x="543" y="150" width="4" height="60" rx="2" fill="#121825" />
            <g clip-path="url(#hero-screen)">
                <rect x="332" y="68" width="200" height="424" fill="var(--surface)" />

                {{-- Content the code opens (finished frame). --}}
                <g data-part="content">
                    <rect data-part="header" x="332" y="68" width="200" height="132" fill="url(#hero-brand)" />
                    <circle data-part="header" cx="374" cy="152" r="22" fill="#ffffff" opacity="0.95" />
                    <rect data-part="header" x="364" y="142" width="20" height="20" rx="6" fill="none" stroke="var(--color-brand-600)" stroke-width="4" />
                    <rect data-part="header" x="408" y="140" width="96" height="10" rx="5" fill="#ffffff" />
                    <rect data-part="header" x="408" y="158" width="64" height="8" rx="4" fill="#ffffff" opacity="0.7" />
                    @foreach ($rows as [$y, $dot])
                        <g data-part="row">
                            <rect x="348" y="{{ $y }}" width="168" height="46" rx="12" fill="var(--surface-sunken)" />
                            <circle cx="372" cy="{{ $y + 23 }}" r="11" fill="{{ $dot }}" />
                            <rect x="392" y="{{ $y + 14 }}" width="86" height="8" rx="4" fill="var(--ink)" opacity="0.75" />
                            <rect x="392" y="{{ $y + 28 }}" width="52" height="6" rx="3" fill="var(--ink-subtle)" opacity="0.6" />
                        </g>
                    @endforeach
                    <g data-part="cta">
                        <rect x="348" y="420" width="168" height="44" rx="14" fill="var(--color-brand-600)" />
                        <rect x="400" y="438" width="64" height="8" rx="4" fill="#ffffff" />
                    </g>
                </g>

                {{-- Camera view (first frames). --}}
                <rect data-part="camera" x="332" y="68" width="200" height="424" fill="#0b1220" opacity="0" />
                <g data-part="finder" opacity="0">
                    <rect x="372" y="208" width="120" height="120" rx="10" fill="#ffffff" />
                    <path d="{{ $qr['path'] }}" fill="#121825" transform="translate(380 216) scale({{ $finderScale }})" />
                </g>
                <g data-part="particles" transform="translate(380 216) scale({{ $finderScale }})">
                    @foreach ($qr['particles'] as $particle)
                        <rect data-part="particle" data-dx="{{ $particle['dx'] }}" data-dy="{{ $particle['dy'] }}" x="{{ $particle['x'] }}" y="{{ $particle['y'] }}" width="1" height="1" fill="var(--color-brand-400)" opacity="0" />
                    @endforeach
                </g>
                <g data-part="brackets" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" opacity="0">
                    <path d="M360 218v-14a10 10 0 0 1 10-10h14M480 194h14a10 10 0 0 1 10 10v14M504 318v14a10 10 0 0 1-10 10h-14M384 342h-14a10 10 0 0 1-10-10v-14" />
                </g>
                <g data-part="scan" opacity="0">
                    <rect x="352" y="178" width="160" height="24" fill="url(#hero-beam)" />
                    <rect x="352" y="200" width="160" height="3" rx="1.5" fill="var(--color-brand-400)" />
                </g>
            </g>
            <rect x="402" y="78" width="60" height="16" rx="8" fill="#121825" />
        </g>

        {{-- Floating details (finished frame). --}}
        <g data-part="chip">
            <circle cx="300" cy="118" r="28" fill="var(--surface)" stroke="var(--line)" stroke-width="1.5" />
            {{-- A check mark is not directional: flip it back inside the mirrored drawing. --}}
            <path d="M287 119l9 9 17-18" fill="none" stroke="var(--color-success-600)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="rtl:-scale-x-100 [transform-box:fill-box] [transform-origin:center]" />
        </g>
        <g data-part="chip">
            <rect x="476" y="432" width="114" height="88" rx="18" fill="var(--surface)" stroke="var(--line)" stroke-width="1.5" />
            @foreach ([[494, 24], [516, 40], [538, 30], [560, 52]] as [$x, $height])
                <rect data-part="bar" x="{{ $x }}" y="{{ 502 - $height }}" width="14" height="{{ $height }}" rx="4" fill="url(#hero-brand)" />
            @endforeach
        </g>
    </svg>
</div>
