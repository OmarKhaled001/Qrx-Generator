{{-- Toasts from session('toast') and from Livewire: $this->dispatch('toast', message: ..., type: ...). --}}
@php($initial = session()->has('toast') ? [session('toast')] : [])
<div
    x-data="toasts(@js($initial))"
    x-on:toast.window="add($event.detail)"
    class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex flex-col items-center gap-2 p-4 sm:items-end"
    aria-live="polite"
>
    <template x-for="toast in items" :key="toast.id">
        <div
            x-transition
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-md border border-line bg-raised p-4 text-sm text-ink shadow-pop"
            role="status"
        >
            <span class="mt-0.5 size-2 shrink-0 rounded-full" :class="toast.type === 'danger' ? 'bg-danger-600' : (toast.type === 'warning' ? 'bg-warning-600' : 'bg-success-600')" aria-hidden="true"></span>
            <p class="min-w-0 flex-1" x-text="toast.message"></p>
            <button type="button" class="text-ink-subtle hover:text-ink" x-on:click="remove(toast.id)" aria-label="{{ __('Dismiss') }}">
                <x-heroicon-o-x-mark class="size-4" aria-hidden="true" />
            </button>
        </div>
    </template>
</div>
