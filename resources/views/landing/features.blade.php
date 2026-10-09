{{-- The eight QR code types. --}}
<section id="features" class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28">
    <x-landing.heading :eyebrow="__('Features')" :title="__('One tool for every kind of QR code')" :description="__('Every code is dynamic: it opens a short link you control, so you can fix a typo or point it somewhere new without reprinting.')" />
    <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach (\App\Enums\QrType::cases() as $type)
            <div class="group rounded-2xl border border-line bg-surface p-5 shadow-card transition-[box-shadow,transform,border-color] duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-raised">
                <span class="grid size-11 place-items-center rounded-xl bg-primary-soft text-primary-ink transition-colors duration-300 group-hover:bg-primary group-hover:text-on-primary">
                    <x-dynamic-component :component="$type->icon()" class="size-5" aria-hidden="true" />
                </span>
                <h3 class="mt-4 font-semibold text-ink">{{ $type->label() }}</h3>
                <p class="mt-1 text-sm text-ink-muted">{{ $type->description() }}</p>
            </div>
        @endforeach
    </div>
</section>
