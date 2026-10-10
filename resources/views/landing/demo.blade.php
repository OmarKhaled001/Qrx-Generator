{{-- Interactive mini builder: no account, nothing stored. Style gallery buttons above load presets into it. --}}
<section id="demo">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28">
        <x-landing.heading :eyebrow="__('Live demo')" :title="__('Try it now')" :description="__('Type anything, pick a style and download your code. No account needed.')" />
        <div class="mt-12">
            <livewire:public.qr-demo />
        </div>
    </div>
</section>
