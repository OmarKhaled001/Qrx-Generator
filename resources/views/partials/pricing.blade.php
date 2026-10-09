{{-- Plan cards for the landing and billing pages. Expects $plans; $currentPlan is optional. --}}
@php($currentPlan ??= null)
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
    @foreach ($plans as $plan)
        @php($isCurrent = $currentPlan?->is($plan) || ($currentPlan?->is_free && $plan->is_free))
        @php($featured = $plan->slug === 'business')
        <article @class([
            'relative flex flex-col rounded-xl border bg-surface p-6 shadow-card',
            'border-primary ring-1 ring-primary' => $featured,
            'border-line' => ! $featured,
        ])>
            @if ($featured)
                <x-ui.badge color="primary" class="absolute -top-3 start-6">{{ __('Most popular') }}</x-ui.badge>
            @endif
            <h3 class="text-lg font-semibold text-ink">{{ __($plan->name) }}</h3>
            <p class="mt-4 flex items-baseline gap-1">
                <span class="text-4xl font-semibold tracking-tight text-ink">{{ $plan->is_free ? __('Free') : $plan->formattedPrice() }}</span>
                @unless ($plan->is_free)<span class="text-sm text-ink-muted">{{ __('/ month') }}</span>@endunless
            </p>
            <ul class="mt-6 flex-1 space-y-3 text-sm text-ink-muted">
                @foreach ($plan->features ?? [] as $feature)
                    <li class="flex gap-2"><x-heroicon-o-check class="mt-0.5 size-4 shrink-0 text-success-600" aria-hidden="true" />{{ __($feature) }}</li>
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
