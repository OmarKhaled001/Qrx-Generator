{{-- Final call to action. --}}
<section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:pb-28">
    <div class="relative overflow-hidden rounded-[2rem] bg-brand-gradient-deep px-6 py-14 text-center text-white shadow-raised sm:px-12 sm:py-20">
        <div class="absolute inset-0 opacity-15 [background-image:linear-gradient(#fff_1px,transparent_1px),linear-gradient(90deg,#fff_1px,transparent_1px)] [background-size:40px_40px] [mask-image:radial-gradient(closest-side,#000,transparent)]" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Make your next QR code the last one you reprint') }}</h2>
            <p class="mt-4 text-lg text-white/85">{{ __('Create it in a minute, print it once and keep it up to date for as long as it hangs on the wall.') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ auth()->check() ? route('qr.create') : route('register') }}" class="inline-flex h-12 items-center gap-2 rounded-md bg-white px-6 font-semibold text-neutral-900 shadow-pop transition-transform hover:-translate-y-0.5 active:scale-[0.97]">
                    {{ __('Create a free QR code') }}
                    <x-heroicon-m-arrow-right class="size-5 rtl:-scale-x-100" aria-hidden="true" />
                </a>
                <a href="#contact" class="inline-flex h-12 items-center rounded-md px-6 font-semibold text-white ring-1 ring-white/50 transition-colors ring-inset hover:bg-white/10">{{ __('Talk to us') }}</a>
            </div>
            <p class="mt-5 text-sm text-white/75">{{ __('Free plan, no card needed.') }}</p>
        </div>
    </div>
</section>
