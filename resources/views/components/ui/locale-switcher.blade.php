@php($current = app()->getLocale())
<x-ui.dropdown {{ $attributes }} width="w-40">
    <x-slot:trigger>
        <button type="button" class="inline-flex h-9 items-center gap-1.5 rounded-md px-2.5 text-sm font-medium text-ink-muted transition hover:bg-sunken hover:text-ink" aria-label="{{ __('Language') }}">
            <x-heroicon-o-language class="size-5" aria-hidden="true" />
            <span>{{ mb_strtoupper($current) }}</span>
        </button>
    </x-slot:trigger>
    @foreach (\App\Support\Locales::supported() as $locale)
        <form method="POST" action="{{ route('locale.update', $locale) }}">
            @csrf
            <button type="submit" role="menuitem" lang="{{ $locale }}" @class([
                'flex w-full items-center justify-between px-3 py-2 text-start text-sm transition hover:bg-sunken',
                'font-semibold text-primary-ink' => $locale === $current,
                'text-ink' => $locale !== $current,
            ])>
                {{ \App\Support\Locales::name($locale) }}
                @if ($locale === $current)<x-heroicon-o-check class="size-4" aria-hidden="true" />@endif
            </button>
        </form>
    @endforeach
</x-ui.dropdown>
