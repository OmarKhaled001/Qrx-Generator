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
        'placeholder:text-ink-subtle/80 hover:border-ink-subtle/60 focus:border-primary focus:ring-4 focus:ring-primary/15 focus:outline-none',
        'border-danger-600' => $invalid,
        'border-line-strong' => ! $invalid,
        // Emails, links and numbers keep dir="ltr" but line up with the other fields in Arabic.
        'rtl:text-end' => $attributes->get('dir') === 'ltr',
    ]) }}
>
