{{-- Brand logo, same drawing as public/brand/qrx-logo.svg (see docs/BRAND.md). The wordmark takes the text colour. --}}
@props(['mark' => false])
@php($gradient = 'qrx-tile-'.\Illuminate\Support\Str::random(6))
<svg {{ $attributes->class('h-8 w-auto shrink-0') }} viewBox="0 0 {{ $mark ? 48 : 116 }} 48" role="img" aria-label="{{ config('app.name') }}">
    <defs>
        <linearGradient id="{{ $gradient }}" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#00a5ab" />
            <stop offset="1" stop-color="#7543e9" />
        </linearGradient>
    </defs>
    <rect width="48" height="48" rx="12" fill="url(#{{ $gradient }})" />
    <rect x="9.75" y="9.75" width="21.5" height="21.5" rx="6.5" fill="none" stroke="#fff" stroke-width="4.5" />
    <g fill="#fff">
        <rect x="16.5" y="16.5" width="8" height="8" rx="2.25" />
        <rect x="28.5" y="28.5" width="6.5" height="6.5" rx="1.75" />
        <rect x="37" y="37" width="5" height="5" rx="1.5" />
    </g>
    @unless ($mark)
        <g fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="60" y="15" width="15.5" height="18" rx="6" />
            <path d="M75.5 15V42M85 15V33M85 24C85 18.5 88 15 93 15M100 15L113 33M113 15L100 33" />
        </g>
    @endunless
</svg>
