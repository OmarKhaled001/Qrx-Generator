{{-- Landing page. Sections live in resources/views/landing. No testimonials, logos or usage numbers until there are real ones. --}}
<x-layouts::public :description="__('Dynamic QR codes you can edit, track and brand.')">
    @include('landing.hero')
    @include('landing.how-it-works')
    @include('landing.use-cases')
    @include('landing.features')
    @include('landing.styles')
    @include('landing.demo')
    @include('landing.analytics')
    @include('landing.pricing')
    @include('landing.faq')
    @include('landing.contact')
    @include('landing.cta')
</x-layouts::public>
