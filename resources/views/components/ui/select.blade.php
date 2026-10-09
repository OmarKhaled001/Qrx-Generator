@props(['name' => null, 'id' => null])
@php
    $id ??= $name;
    $invalid = $name && $errors->has($name);
@endphp
<select
    @if ($id) id="{{ $id }}" @endif
    @if ($name) name="{{ $name }}" @endif
    @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
    {{ $attributes->class([
        'block h-10 w-full rounded-md border bg-surface ps-3 pe-8 text-sm text-ink shadow-xs transition',
        'focus:border-primary focus:ring-3 focus:ring-primary/20 focus:outline-none',
        'border-danger-600' => $invalid,
        'border-line-strong' => ! $invalid,
    ]) }}
>{{ $slot }}</select>
