{{-- How it works: three steps. --}}
@php
    $steps = [
        ['icon' => 'heroicon-o-squares-plus', 'title' => __('Create'), 'text' => __('Pick one of 8 types and add your link, Wi-Fi details, contact card or message.')],
        ['icon' => 'heroicon-o-swatch', 'title' => __('Style and print'), 'text' => __('Match your brand colours, check the contrast, then download PNG, SVG or print-ready EPS.')],
        ['icon' => 'heroicon-o-arrow-path', 'title' => __('Update and track'), 'text' => __('Change where the code points at any time and see when, where and on which devices it is scanned.')],
    ];
@endphp
<section id="how-it-works" class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28">
    <x-landing.heading center :eyebrow="__('How it works')" :title="__('From idea to printed code in three steps')" :description="__('No design skills needed. Your code stays editable after it is printed.')" />

    <ol class="relative mt-14 grid gap-10 md:grid-cols-3 md:gap-6">
        {{-- Connector between the step icons (wide screens). --}}
        <span class="absolute inset-x-[16%] top-7 hidden border-t-2 border-dashed border-line-strong md:block" aria-hidden="true"></span>
        @foreach ($steps as $index => $step)
            <li class="relative flex flex-col items-center text-center">
                <span class="relative grid size-14 place-items-center rounded-2xl bg-brand-gradient text-white shadow-glow">
                    <x-dynamic-component :component="$step['icon']" class="size-7" aria-hidden="true" />
                    <span class="absolute -end-2 -top-2 grid size-6 place-items-center rounded-full border-2 border-page bg-ink text-xs font-bold text-page tabular-nums">{{ Number::format($index + 1) }}</span>
                </span>
                <h3 class="mt-6 text-xl font-semibold text-ink">{{ $step['title'] }}</h3>
                <p class="mt-2 max-w-xs text-ink-muted">{{ $step['text'] }}</p>
            </li>
        @endforeach
    </ol>
</section>
