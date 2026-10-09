<x-layouts::public>
    <section class="mx-auto max-w-3xl px-4 py-24 text-center sm:px-6">
        <x-ui.logo mark class="mx-auto" />
        <h1 class="mt-6 text-4xl font-semibold tracking-tight text-ink sm:text-5xl">{{ __('Dynamic QR codes you can edit, track and brand.') }}</h1>
        <div class="mt-8 flex justify-center gap-3">
            @if (Route::has('register'))
                <x-ui.button :href="route('register')" size="lg">{{ __('Get started') }}</x-ui.button>
            @endif
            @if (Route::has('login'))
                <x-ui.button :href="route('login')" variant="secondary" size="lg">{{ __('Log in') }}</x-ui.button>
            @endif
        </div>
    </section>
</x-layouts::public>
