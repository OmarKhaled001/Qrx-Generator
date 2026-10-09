<x-layouts::public :title="__('Unavailable')" minimal>
    <div class="mx-auto flex max-w-md flex-col items-center px-4 py-16 text-center sm:py-24">
        <span class="grid size-16 place-items-center rounded-full bg-sunken text-ink-muted">
            @if ($reason === 'missing')
                <x-heroicon-o-question-mark-circle class="size-8" aria-hidden="true" />
            @else
                <x-heroicon-o-pause-circle class="size-8" aria-hidden="true" />
            @endif
        </span>
        <h1 class="mt-6 text-2xl font-semibold text-ink">
            {{ $reason === 'missing' ? __('We could not find this code') : __('This code is temporarily unavailable') }}
        </h1>
        <p class="mt-2 text-ink-muted">
            {{ $reason === 'missing' ? __('Check that the link is complete, or ask whoever shared it.') : __('Its owner has paused it for now. Please try again later.') }}
        </p>
        <x-ui.button :href="url('/')" variant="secondary" class="mt-8">{{ __('Go to :app', ['app' => config('app.name')]) }}</x-ui.button>
    </div>
</x-layouts::public>
