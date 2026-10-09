{{-- Landing page contact form (L4). Included by home.blade.php after pricing. --}}
<section id="contact" class="border-t border-line">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1fr_1.4fr]">
        <div>
            <h2 class="text-3xl font-semibold tracking-tight text-ink">{{ __('Questions? Talk to us') }}</h2>
            <p class="mt-3 text-ink-muted">{{ __('Bulk codes, custom plans or anything else: send us a message.') }}</p>
        </div>
        <livewire:public.contact-form />
    </div>
</section>
