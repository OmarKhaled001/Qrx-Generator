{{-- Server-rendered SVG bar chart with a screen-reader table. $series: label => value (time runs left to right in every language). --}}
@props(['series', 'label', 'height' => 140])
@php
    $values = array_values($series);
    $labels = array_keys($series);
    $max = max(1, ...$values);
    $count = max(1, count($values));
    $gap = 2;
    $barWidth = 10;
    $width = $count * ($barWidth + $gap);
@endphp
<figure {{ $attributes->class('w-full') }}>
    <svg viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="none" class="h-36 w-full" dir="ltr" role="img" aria-label="{{ $label }}">
        <line x1="0" y1="{{ $height - 0.5 }}" x2="{{ $width }}" y2="{{ $height - 0.5 }}" stroke="var(--line)" stroke-width="1" />
        @foreach ($values as $index => $value)
            @php($barHeight = $value > 0 ? max(2, round($value / $max * ($height - 8))) : 0)
            <rect
                x="{{ $index * ($barWidth + $gap) }}"
                y="{{ $height - $barHeight }}"
                width="{{ $barWidth }}"
                height="{{ $barHeight }}"
                rx="2"
                fill="var(--primary)"
                opacity="{{ $index === $count - 1 ? 1 : 0.75 }}"
            ><title>{{ $labels[$index] }}: {{ Number::format($value) }}</title></rect>
        @endforeach
    </svg>
    <figcaption class="mt-2 flex justify-between text-xs text-ink-subtle" dir="ltr">
        <span>{{ \Illuminate\Support\Carbon::parse($labels[0])->translatedFormat('j M') }}</span>
        <span>{{ \Illuminate\Support\Carbon::parse(end($labels))->translatedFormat('j M') }}</span>
    </figcaption>
    <table class="sr-only">
        <caption>{{ $label }}</caption>
        @foreach ($series as $day => $value)
            <tr><th scope="row">{{ $day }}</th><td>{{ $value }}</td></tr>
        @endforeach
    </table>
</figure>
