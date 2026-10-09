{{-- Landing page: live demo and contact form (L4). Included by home.blade.php. --}}
<section id="demo" class="border-t border-line">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-semibold tracking-tight text-ink">{{ __('Try it now') }}</h2>
            <p class="mt-3 text-ink-muted">{{ __('Type anything, pick a style and download your code. No account needed.') }}</p>
        </div>
        <div class="mt-10">
            <livewire:public.qr-demo />
        </div>
    </div>
</section>
