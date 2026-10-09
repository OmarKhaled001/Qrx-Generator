<button
    type="button"
    x-data
    x-on:click="$store.theme.toggle()"
    :aria-pressed="$store.theme.dark"
    aria-label="{{ __('Dark mode') }}"
    {{ $attributes->class('grid size-9 place-items-center rounded-md text-ink-muted transition hover:bg-sunken hover:text-ink') }}
>
    <x-heroicon-o-moon x-show="! $store.theme.dark" class="size-5" aria-hidden="true" />
    <x-heroicon-o-sun x-show="$store.theme.dark" x-cloak class="size-5" aria-hidden="true" />
</button>
