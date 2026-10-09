{{-- Hero. On large screens it stays pinned while the illustration plays (resources/js/hero.js). --}}
<section data-hero class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[36rem] bg-glow" aria-hidden="true"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-4 pt-12 pb-16 sm:px-6 sm:pt-16 lg:min-h-[calc(100dvh-4rem)] lg:grid-cols-[1fr_1.05fr] lg:gap-6 lg:py-10">
        <div class="max-w-xl">
            <x-ui.badge color="primary" dot>{{ __('Dynamic QR codes') }}</x-ui.badge>
            <h1 class="mt-5 text-4xl font-bold tracking-tight text-ink sm:text-5xl lg:text-6xl lg:leading-[1.05]">{{ __('Print once. Change it any time.') }}</h1>
            <p class="mt-6 text-lg text-ink-muted sm:text-xl">{{ __('Create branded QR codes for links, Wi-Fi, contact cards, menus and more. Edit where they point after printing and see how often they are scanned.') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-ui.button :href="auth()->check() ? route('qr.create') : route('register')" size="lg" icon="heroicon-o-plus">{{ __('Create a free QR code') }}</x-ui.button>
                <x-ui.button href="#how-it-works" variant="secondary" size="lg">{{ __('See how it works') }}</x-ui.button>
            </div>
            <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-ink-muted">
                @php($freeCodes = $plans->firstWhere('is_free', true)?->qr_limit ?? \App\Models\Plan::free()->qr_limit)
                @foreach ([__('No card needed'), trans_choice(':count free code|:count free codes', $freeCodes, ['count' => Number::format($freeCodes)]), __('Edit after printing')] as $point)
                    <li class="flex items-center gap-1.5"><x-heroicon-m-check-circle class="size-4 text-success-600" aria-hidden="true" />{{ $point }}</li>
                @endforeach
            </ul>
        </div>

        @include('landing.hero-scene')
    </div>

    <div class="pointer-events-none absolute inset-x-0 bottom-5 hidden justify-center text-ink-subtle lg:flex" aria-hidden="true">
        <x-heroicon-o-chevron-double-down class="size-6 motion-safe:animate-bounce" />
    </div>
</section>
