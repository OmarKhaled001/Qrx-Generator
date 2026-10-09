@props(['type' => 'info', 'heading' => null])
@php
    $icon = match ($type) {
        'success' => 'heroicon-o-check-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'danger' => 'heroicon-o-x-circle',
        default => 'heroicon-o-information-circle',
    };
@endphp
<div role="{{ $type === 'danger' ? 'alert' : 'status' }}" {{ $attributes->class([
    'flex gap-3 rounded-md border p-4 text-sm',
    match ($type) {
        'success' => 'border-success-100 bg-success-50 text-success-900 dark:border-success-900 dark:bg-success-900/30 dark:text-success-100',
        'warning' => 'border-warning-100 bg-warning-50 text-warning-900 dark:border-warning-900 dark:bg-warning-900/30 dark:text-warning-100',
        'danger' => 'border-danger-100 bg-danger-50 text-danger-900 dark:border-danger-900 dark:bg-danger-900/30 dark:text-danger-100',
        default => 'border-info-100 bg-info-50 text-info-900 dark:border-info-900 dark:bg-info-900/30 dark:text-info-100',
    },
]) }}>
    <x-dynamic-component :component="$icon" class="size-5 shrink-0" aria-hidden="true" />
    <div class="min-w-0 space-y-1">
        @if ($heading)<p class="font-semibold">{{ $heading }}</p>@endif
        <div>{{ $slot }}</div>
    </div>
</div>
