<x-layouts::public :description="__('Dynamic QR codes you can edit, track and brand.')">
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-brand-100/60 via-transparent to-transparent dark:from-brand-950/50" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
            <div>
                <x-ui.badge color="primary">{{ __('Dynamic QR codes') }}</x-ui.badge>
                <h1 class="mt-5 text-4xl font-semibold tracking-tight text-ink sm:text-5xl lg:text-6xl">{{ __('Print once. Change it any time.') }}</h1>
                <p class="mt-5 max-w-xl text-lg text-ink-muted">{{ __('Create branded QR codes for links, Wi-Fi, contact cards, menus and more. Edit where they point after printing and see how often they are scanned.') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-ui.button :href="auth()->check() ? route('qr.create') : route('register')" size="lg" icon="heroicon-o-plus">{{ __('Create a free QR code') }}</x-ui.button>
                    <x-ui.button href="#pricing" variant="secondary" size="lg">{{ __('See pricing') }}</x-ui.button>
                </div>
                <p class="mt-4 text-sm text-ink-subtle">{{ __('Start free with 3 dynamic QR codes. No card needed.') }}</p>
            </div>
            <div class="relative mx-auto w-full max-w-sm" aria-hidden="true">
                <div class="absolute -inset-6 rounded-[2rem] bg-linear-to-tr from-brand-400/30 to-accent-400/30 blur-2xl"></div>
                <div class="relative rounded-2xl border border-line bg-surface p-6 shadow-pop">
                    <img src="{{ app(\App\Services\QrRenderer::class)->svgDataUri(url('/'), ['style' => 'round', 'eye' => 'circle', 'gradient' => ['from' => '#0e7490', 'to' => '#7c3aed']], 360) }}" alt="" class="aspect-square w-full">
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="font-medium text-ink">{{ __('Restaurant menu') }}</span>
                        <x-ui.badge color="success" dot>{{ __('Active') }}</x-ui.badge>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Types --}}
    <section id="features" class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-semibold tracking-tight text-ink">{{ __('One tool for every kind of QR code') }}</h2>
            <p class="mt-3 text-ink-muted">{{ __('Every code is dynamic: it opens a short link you control, so you can fix a typo or point it somewhere new without reprinting.') }}</p>
        </div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (\App\Enums\QrType::cases() as $type)
                <div class="rounded-lg border border-line bg-surface p-5 shadow-card">
                    <span class="grid size-10 place-items-center rounded-md bg-primary-soft text-primary-ink">
                        <x-dynamic-component :component="$type->icon()" class="size-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-4 font-semibold text-ink">{{ $type->label() }}</h3>
                    <p class="mt-1 text-sm text-ink-muted">{{ $type->description() }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @includeWhen(View::exists('partials.landing-extra'), 'partials.landing-extra')

    {{-- Pricing --}}
    <section id="pricing" class="border-t border-line bg-sunken/50">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-semibold tracking-tight text-ink">{{ __('Simple pricing') }}</h2>
                <p class="mt-3 text-ink-muted">{{ __('Start free. Upgrade when you need more codes or scans. Cancel any time.') }}</p>
            </div>
            <div class="mt-12">
                @include('partials.pricing', ['plans' => $plans, 'currentPlan' => $currentPlan])
            </div>
        </div>
    </section>
    @includeWhen(View::exists('partials.landing-contact'), 'partials.landing-contact')
</x-layouts::public>
