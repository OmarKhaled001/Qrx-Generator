@props(['name' => null, 'id' => null, 'label'])
@php($id ??= $name)
<label for="{{ $id }}" class="inline-flex items-center gap-2 text-sm text-ink">
    <input
        type="checkbox"
        @if ($id) id="{{ $id }}" @endif
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('size-4 rounded border-line-strong text-primary accent-(--primary) focus:ring-primary/30') }}
    >
    <span>{{ $label }}</span>
</label>
