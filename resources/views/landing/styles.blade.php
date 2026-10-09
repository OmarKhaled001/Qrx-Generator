{{-- Style gallery: every preset as a real, scannable sample. "Try this style" loads it into the demo below. --}}
<section id="styles" class="border-y border-line bg-surface">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28">
        <x-landing.heading :eyebrow="__('Styles')" :title="__('Styles that still scan')" :description="__('Start from a ready-made look, then fine-tune colours, dots and corners. You get a warning if the contrast gets too low to scan.')" />

        <ul class="mt-12 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3">
            @foreach (\App\Support\QrPresets::all() as $key => $preset)
                <li class="group rounded-2xl border border-line bg-page p-4 shadow-card transition-[box-shadow,transform] duration-300 hover:-translate-y-1 hover:shadow-raised sm:p-5">
                    <div class="grid place-items-center rounded-xl p-4 transition-transform duration-300 group-hover:scale-[1.02]" style="background-color: {{ $preset['bg'] }}">
                        <img src="{{ $samples[$key] }}" alt="{{ __('Sample QR code in the :style style', ['style' => $preset['label']]) }}" class="aspect-square w-full max-w-48" loading="lazy">
                    </div>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-semibold text-ink">{{ $preset['label'] }}</h3>
                        <a href="#demo" x-data x-on:click="Livewire.dispatch('apply-preset', { key: '{{ $key }}' })"
                            class="inline-flex items-center gap-1 rounded-md text-sm font-semibold text-primary-ink hover:underline">
                            {{ __('Try this style') }}
                            <x-heroicon-m-arrow-down class="size-4" aria-hidden="true" />
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
