@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination') }}" class="flex items-center justify-between gap-4">
        <p class="text-sm text-ink-muted">
            {{ __('Showing :first–:last of :total', ['first' => $paginator->firstItem(), 'last' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="grid size-9 place-items-center rounded-md text-ink-subtle opacity-50" aria-hidden="true">
                    <x-heroicon-o-chevron-left class="size-4 rtl:rotate-180" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" wire:navigate class="grid size-9 place-items-center rounded-md text-ink-muted hover:bg-sunken" aria-label="{{ __('Previous page') }}">
                    <x-heroicon-o-chevron-left class="size-4 rtl:rotate-180" aria-hidden="true" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-ink-subtle">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="grid size-9 place-items-center rounded-md bg-primary text-sm font-semibold text-on-primary">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" wire:navigate class="grid size-9 place-items-center rounded-md text-sm text-ink-muted hover:bg-sunken" aria-label="{{ __('Page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" wire:navigate class="grid size-9 place-items-center rounded-md text-ink-muted hover:bg-sunken" aria-label="{{ __('Next page') }}">
                    <x-heroicon-o-chevron-right class="size-4 rtl:rotate-180" aria-hidden="true" />
                </a>
            @else
                <span class="grid size-9 place-items-center rounded-md text-ink-subtle opacity-50" aria-hidden="true">
                    <x-heroicon-o-chevron-right class="size-4 rtl:rotate-180" />
                </span>
            @endif
        </div>
    </nav>
@endif
