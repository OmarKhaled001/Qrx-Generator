@props(['label', 'for', 'hint' => null, 'error' => null, 'required' => false])
@php($message = $errors->first($error ?? $for))
<div {{ $attributes->class('space-y-1.5') }}>
    <label for="{{ $for }}" class="block text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)<span class="text-danger-600" aria-hidden="true">*</span>@endif
    </label>
    {{ $slot }}
    @if ($message)
        <p id="{{ $for }}-error" class="text-xs font-medium text-danger-600" role="alert">{{ $message }}</p>
    @elseif ($hint)
        <p id="{{ $for }}-hint" class="text-xs text-ink-subtle">{{ $hint }}</p>
    @endif
</div>
