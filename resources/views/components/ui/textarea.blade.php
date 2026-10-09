@props(['name' => null, 'id' => null, 'rows' => 3])
@php
    $id ??= $name;
    $invalid = $name && $errors->has($name);
@endphp
<textarea
    @if ($id) id="{{ $id }}" @endif
    @if ($name) name="{{ $name }}" @endif
    rows="{{ $rows }}"
    @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
    {{ $attributes->class([
        'block w-full rounded-md border bg-surface px-3 py-2 text-sm text-ink shadow-xs transition',
        'placeholder:text-ink-subtle focus:border-primary focus:ring-3 focus:ring-primary/20 focus:outline-none',
        'border-danger-600' => $invalid,
        'border-line-strong' => ! $invalid,
    ]) }}
>{{ $slot }}</textarea>
