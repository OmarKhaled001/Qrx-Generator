{{-- A colour swatch and a hex text field bound to the same Livewire property. --}}
@props(['model', 'id', 'label'])
<div {{ $attributes->class('space-y-1.5') }}>
    <label for="{{ $id }}" class="block text-sm font-medium text-ink">{{ $label }}</label>
    <div class="flex items-center gap-2">
        <input type="color" wire:model.live.debounce.150ms="{{ $model }}" class="h-10 w-12 shrink-0 cursor-pointer rounded-md border border-line-strong bg-surface p-1" aria-label="{{ $label }}">
        <x-ui.input :id="$id" :name="$model" wire:model.live.debounce.400ms="{{ $model }}" dir="ltr" maxlength="7" class="font-mono uppercase" />
    </div>
    @error($model)<p id="{{ $id }}-error" class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>@enderror
</div>
