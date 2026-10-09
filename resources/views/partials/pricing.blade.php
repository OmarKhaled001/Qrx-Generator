{{-- Plan cards for the landing and billing pages. Expects $plans; $currentPlan is optional. --}}
@php($currentPlan ??= null)
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
    @foreach ($plans as $plan)
        @php($isCurrent = $currentPlan?->is($plan) || ($currentPlan?->is_free && $plan->is_free))
        @php($featured = $plan->slug === 'business')
        <article @class([
            'relative flex flex-col rounded-2xl border bg-surface p-6 transition-[box-shadow,transform] duration-300 hover:-translate-y-1',
            'border-primary shadow-raised ring-1 ring-primary hover:shadow-glow' => $featured,
            'border-line shadow-card hover:shadow-raised' => ! $featured,
        ])>
            @if ($featured)
                <span class="absolute -top-3 start-6 rounded-full bg-brand-gradient-deep px-3 py-0.5 text-xs font-semibold text-white shadow-glow">{{ __('Most popular') }}</span>
            @endif
            <h3 class="text-lg font-semibold text-ink">{{ __($plan->name) }}</h3>
            <p class="mt-4 flex items-baseline gap-1">
                <span class="text-4xl font-bold tracking-tight text-ink tabular-nums">{{ $plan->formattedPrice() }}</span>
                <span class="text-sm text-ink-muted">{{ __('/ month') }}</span>
            </p>
            <ul class="mt-6 flex-1 space-y-3 text-sm text-ink-muted">
                @foreach ($plan->features ?? [] as $feature)
                    <li class="flex gap-2"><x-heroicon-m-check-circle class="mt-0.5 size-4 shrink-0 text-success-600" aria-hidden="true" />{{ __($feature) }}</li>
                @endforeach
            </ul>
            <div class="mt-8">
                @if ($isCurrent)
                    <x-ui.button variant="secondary" class="w-full" disabled>{{ __('Current plan') }}</x-ui.button>
                @elseif (! auth()->check())
                    <x-ui.button :href="route('register')" :variant="$featured ? 'primary' : 'secondary'" class="w-full">{{ $plan->is_free ? __('Start free') : __('Get :plan', ['plan' => __($plan->name)]) }}</x-ui.button>
                @elseif (! $plan->is_free && Route::has('billing.checkout'))
                    <x-ui.button :href="route('billing.checkout', $plan)" :variant="$featured ? 'primary' : 'secondary'" class="w-full">{{ __('Upgrade to :plan', ['plan' => __($plan->name)]) }}</x-ui.button>
                @endif
            </div>
        </article>
    @endforeach
</div>
