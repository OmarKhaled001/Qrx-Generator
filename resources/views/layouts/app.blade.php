{{-- Client dashboard layout: one sidebar, one top bar, used by every signed-in page. --}}
@props(['title' => null])
@php
    $navigation = collect([
        ['route' => 'dashboard', 'active' => 'dashboard', 'label' => __('Overview'), 'icon' => 'heroicon-o-squares-2x2'],
        ['route' => 'qr.index', 'active' => 'qr.*', 'label' => __('QR codes'), 'icon' => 'heroicon-o-qr-code'],
        ['route' => 'billing', 'active' => 'billing*', 'label' => __('Plan & billing'), 'icon' => 'heroicon-o-credit-card'],
        ['route' => 'profile', 'active' => 'profile', 'label' => __('Profile'), 'icon' => 'heroicon-o-user-circle'],
    ])->filter(fn (array $item) => Route::has($item['route']));
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-surface focus:px-4 focus:py-2 focus:shadow-pop">{{ __('Skip to content') }}</a>

    <div x-data="{ sidebar: false }" class="min-h-dvh lg:grid lg:grid-cols-[16.5rem_minmax(0,1fr)]">
        {{-- Mobile backdrop --}}
        <div x-show="sidebar" x-cloak x-transition.opacity x-on:click="sidebar = false" class="fixed inset-0 z-30 bg-neutral-950/40 lg:hidden"></div>

        <aside
            :class="sidebar ? 'translate-x-0' : '-translate-x-full rtl:translate-x-full'"
            class="fixed inset-y-0 start-0 z-40 flex w-66 flex-col border-e border-line bg-surface transition-transform duration-200 lg:sticky lg:top-0 lg:h-dvh lg:w-auto lg:translate-x-0 lg:rtl:translate-x-0"
            aria-label="{{ __('Main navigation') }}"
        >
            <div class="flex h-16 items-center justify-between px-5">
                <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}"><x-ui.logo /></a>
                <button type="button" class="grid size-9 place-items-center rounded-md text-ink-muted hover:bg-sunken lg:hidden" x-on:click="sidebar = false" aria-label="{{ __('Close menu') }}">
                    <x-heroicon-o-x-mark class="size-5" aria-hidden="true" />
                </button>
            </div>

            @if (Route::has('qr.create'))
                <div class="px-4 pb-2">
                    <x-ui.button :href="route('qr.create')" icon="heroicon-o-plus" class="w-full">{{ __('Create QR code') }}</x-ui.button>
                </div>
            @endif

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3">
                @foreach ($navigation as $item)
                    @php($active = request()->routeIs($item['active']))
                    <a
                        href="{{ route($item['route']) }}"
                        @if ($active) aria-current="page" @endif
                        @class([
                            'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition',
                            'bg-primary-soft text-primary-ink' => $active,
                            'text-ink-muted hover:bg-sunken hover:text-ink' => ! $active,
                        ])
                    >
                        <x-dynamic-component :component="$item['icon']" class="size-5 shrink-0" aria-hidden="true" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            @if ($user?->is_admin)
                <div class="border-t border-line p-3">
                    <a href="{{ url('/admin') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-ink-muted transition hover:bg-sunken hover:text-ink">
                        <x-heroicon-o-shield-check class="size-5 shrink-0" aria-hidden="true" />
                        {{ __('Admin panel') }}
                    </a>
                </div>
            @endif
        </aside>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-2 border-b border-line bg-surface/85 px-4 backdrop-blur sm:px-6">
                <button type="button" class="grid size-9 place-items-center rounded-md text-ink-muted hover:bg-sunken lg:hidden" x-on:click="sidebar = true" aria-label="{{ __('Open menu') }}">
                    <x-heroicon-o-bars-3 class="size-5" aria-hidden="true" />
                </button>
                <div class="flex-1"></div>
                <x-ui.locale-switcher />
                <x-ui.theme-toggle />
                @if ($user)
                    <x-ui.dropdown>
                        <x-slot:trigger>
                            <button type="button" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-ink transition hover:bg-sunken" aria-label="{{ __('Account menu') }}">
                                <span class="grid size-8 place-items-center rounded-full bg-primary-soft font-semibold text-primary-ink" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                <span class="hidden max-w-36 truncate sm:block">{{ $user->name }}</span>
                                <x-heroicon-o-chevron-down class="size-4 text-ink-subtle" aria-hidden="true" />
                            </button>
                        </x-slot:trigger>
                        <div class="border-b border-line px-3 py-2">
                            <p class="truncate text-sm font-medium text-ink">{{ $user->name }}</p>
                            <p class="truncate text-xs text-ink-subtle" dir="ltr">{{ $user->email }}</p>
                        </div>
                        @if (Route::has('profile'))
                            <a href="{{ route('profile') }}" role="menuitem" class="block px-3 py-2 text-sm text-ink hover:bg-sunken">{{ __('Profile') }}</a>
                        @endif
                        @if (Route::has('billing'))
                            <a href="{{ route('billing') }}" role="menuitem" class="block px-3 py-2 text-sm text-ink hover:bg-sunken">{{ __('Plan & billing') }}</a>
                        @endif
                        @if (Route::has('logout'))
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem" class="block w-full px-3 py-2 text-start text-sm text-danger-600 hover:bg-sunken">{{ __('Log out') }}</button>
                            </form>
                        @endif
                    </x-ui.dropdown>
                @endif
            </header>

            <main id="main" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-ui.toast-stack />
</body>
</html>
