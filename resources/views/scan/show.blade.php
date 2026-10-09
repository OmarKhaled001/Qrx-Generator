<x-layouts::public :title="$qrCode->name" minimal>
    <div class="relative">
        <div class="pointer-events-none absolute inset-x-0 -top-16 h-80 bg-glow" aria-hidden="true"></div>
        <div class="relative mx-auto w-full max-w-md px-4 py-6 sm:py-12">
            <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-raised inset-shadow-highlight">
                <div class="h-1.5 bg-brand-gradient" aria-hidden="true"></div>
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-primary-soft text-primary-ink">
                            <x-dynamic-component :component="$qrCode->type->icon()" class="size-6" aria-hidden="true" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold tracking-wide text-primary-ink">{{ $qrCode->type->label() }}</p>
                            <h1 class="truncate text-lg font-bold text-ink" dir="auto">{{ $qrCode->name }}</h1>
                        </div>
                    </div>

                    <div class="mt-6">
                        @include('scan.content', ['type' => $qrCode->type->value, 'payload' => $qrCode->payload])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::public>
