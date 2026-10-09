{{-- Contact form (L4). --}}
<section id="contact" class="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:pb-28">
    <div class="grid gap-10 border-t border-line pt-20 lg:grid-cols-[1fr_1.4fr] lg:pt-28">
        <div>
            <x-landing.heading :eyebrow="__('Contact')" :title="__('Questions? Talk to us')" :description="__('Bulk codes, custom plans or anything else: send us a message.')" />
        </div>
        <livewire:public.contact-form />
    </div>
</section>
