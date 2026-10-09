@props(['name' => null, 'id' => null])
@php
    $id ??= $name;
    $invalid = $name && $errors->has($name);
@endphp
<input
    @if ($id) id="{{ $id }}" @endif
    @if ($name) name="{{ $name }}" @endif
    @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
    {{ $attributes->class([
        'block h-10 w-full rounded-md border bg-surface px-3 text-sm text-ink shadow-xs transition',
        'placeholder:text-ink-subtle focus:border-primary focus:ring-3 focus:ring-primary/20 focus:outline-none',
        'border-danger-600' => $invalid,
        'border-line-strong' => ! $invalid,
    ]) }}
>
