<x-layouts::public :title="$qrCode->name" minimal>
    <div class="mx-auto w-full max-w-md px-4 py-8 sm:py-14">
        <div class="rounded-xl border border-line bg-surface p-6 shadow-card sm:p-8">
            <div class="flex items-center gap-3">
                <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-primary-soft text-primary-ink">
                    <x-dynamic-component :component="$qrCode->type->icon()" class="size-6" aria-hidden="true" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium tracking-wide text-ink-subtle uppercase">{{ $qrCode->type->label() }}</p>
                    <h1 class="truncate text-lg font-semibold text-ink">{{ $qrCode->name }}</h1>
                </div>
            </div>

            <div class="mt-6">
                @include('scan.content', ['type' => $qrCode->type->value, 'payload' => $qrCode->payload])
            </div>
        </div>
        <p class="mt-6 text-center text-xs text-ink-subtle">{{ __('Made with :app', ['app' => config('app.name')]) }}</p>
    </div>
</x-layouts::public>
