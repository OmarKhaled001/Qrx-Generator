@props(['color' => 'neutral', 'dot' => false])
<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
    match ($color) {
        'primary' => 'bg-primary-soft text-primary-ink ring-brand-200 dark:ring-brand-800',
        'success' => 'bg-success-50 text-success-700 ring-success-100 dark:bg-success-900/40 dark:text-success-400 dark:ring-success-900',
        'warning' => 'bg-warning-50 text-warning-700 ring-warning-100 dark:bg-warning-900/40 dark:text-warning-400 dark:ring-warning-900',
        'danger' => 'bg-danger-50 text-danger-700 ring-danger-100 dark:bg-danger-900/40 dark:text-danger-400 dark:ring-danger-900',
        'info' => 'bg-info-50 text-info-700 ring-info-100 dark:bg-info-900/40 dark:text-info-400 dark:ring-info-900',
        default => 'bg-sunken text-ink-muted ring-line',
    },
]) }}>
    @if ($dot)<span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>@endif
    {{ $slot }}
</span>
