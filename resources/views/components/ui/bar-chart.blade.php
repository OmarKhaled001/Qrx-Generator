{{-- Server-rendered SVG bar chart with a screen-reader table. $series: date => value, oldest first, ending today. Time runs in the reading direction: mirrored in RTL. --}}
@props(['series', 'label', 'height' => 140])
@php
    $values = array_values($series);
    $labels = array_keys($series);
    $max = max(1, ...$values);
    $count = max(1, count($values));
    $gap = 3;
    $barWidth = 10;
    $width = $count * ($barWidth + $gap) - $gap;
    $gradient = 'bar-fill-'.\Illuminate\Support\Str::random(6);
@endphp
<figure {{ $attributes->class('w-full') }}>
    <div class="relative pt-5">
        <span class="absolute top-0 start-0 text-xs text-ink-subtle tabular-nums">{{ Number::format($max) }}</span>
        <svg viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="none" class="h-40 w-full overflow-visible rtl:-scale-x-100" role="img" aria-label="{{ $label }}">
            <defs>
                <linearGradient id="{{ $gradient }}" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="var(--color-brand-400)" />
                    <stop offset="1" stop-color="var(--color-brand-600)" />
                </linearGradient>
            </defs>
            <line x1="0" y1="0.5" x2="{{ $width }}" y2="0.5" stroke="var(--line)" stroke-dasharray="3 3" vector-effect="non-scaling-stroke" />
            <line x1="0" y1="{{ $height / 2 }}" x2="{{ $width }}" y2="{{ $height / 2 }}" stroke="var(--line)" stroke-dasharray="3 3" vector-effect="non-scaling-stroke" />
            <line x1="0" y1="{{ $height - 0.5 }}" x2="{{ $width }}" y2="{{ $height - 0.5 }}" stroke="var(--line-strong)" vector-effect="non-scaling-stroke" />
            @foreach ($values as $index => $value)
                @php($barHeight = $value > 0 ? max(3, round($value / $max * $height)) : 0)
                <rect
                    x="{{ $index * ($barWidth + $gap) }}"
                    y="{{ $height - $barHeight }}"
                    width="{{ $barWidth }}"
                    height="{{ $barHeight }}"
                    rx="2.5"
                    fill="url(#{{ $gradient }})"
                    class="{{ $index === $count - 1 ? 'opacity-100' : 'opacity-70' }} transition-opacity hover:opacity-100"
                ><title>{{ \Illuminate\Support\Carbon::parse($labels[$index])->translatedFormat('j M') }}: {{ trans_choice(':count scan|:count scans', $value, ['count' => Number::format($value)]) }}</title></rect>
            @endforeach
        </svg>
    </div>
    <figcaption class="mt-2 flex justify-between text-xs text-ink-subtle">
        <span>{{ \Illuminate\Support\Carbon::parse($labels[0])->translatedFormat('j M') }}</span>
        <span>{{ __('Today') }}</span>
    </figcaption>
    {{-- Wrapped: a table ignores sr-only's 1px height and would stretch the page. --}}
    <div class="sr-only">
        <table>
            <caption>{{ $label }}</caption>
            @foreach ($series as $day => $value)
                <tr><th scope="row">{{ $day }}</th><td>{{ $value }}</td></tr>
            @endforeach
        </table>
    </div>
</figure>
