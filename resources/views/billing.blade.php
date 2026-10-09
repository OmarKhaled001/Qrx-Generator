<x-layouts::app :title="__('Plan & billing')">
    <x-ui.page-header :heading="__('Plan & billing')" :description="__('Your plan, usage and payment details.')">
        @if (auth()->user()->hasStripeId())
            <x-slot:actions>
                <x-ui.button :href="route('billing.portal')" variant="secondary" icon="heroicon-o-credit-card">{{ __('Manage billing') }}</x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @if (request('checkout') === 'success')
        <x-ui.alert type="success" :heading="__('Thank you!')" class="mb-6">
            {{ __('Your payment went through. Your new limits apply as soon as Stripe confirms it, usually within a few seconds.') }}
        </x-ui.alert>
    @endif

    @if ($subscription?->onGracePeriod())
        <x-ui.alert type="warning" class="mb-6">
            {{ __('Your subscription is cancelled and ends on :date. After that you move to the free plan.', ['date' => $subscription->ends_at->translatedFormat('j F Y')]) }}
        </x-ui.alert>
    @elseif ($subscription?->pastDue())
        <x-ui.alert type="danger" class="mb-6">{{ __('Your last payment failed. Update your payment method to keep your plan.') }}</x-ui.alert>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat :label="__('Current plan')" :value="__($plan->name)" :hint="$plan->is_free ? __('Free forever') : __(':price / month', ['price' => $plan->formattedPrice()])" icon="heroicon-o-sparkles" />
        <x-ui.stat :label="__('QR codes')" :value="Number::format($qrCount)" :hint="__(':used of :limit used', ['used' => Number::format($qrCount), 'limit' => Number::format($plan->qr_limit)])" icon="heroicon-o-qr-code" />
        <x-ui.stat :label="__('Scans per QR code')" :value="Number::format($plan->scan_limit)" icon="heroicon-o-cursor-arrow-rays" />
    </div>

    <h2 class="mt-12 mb-6 text-lg font-semibold text-ink">{{ $plan->is_free ? __('Upgrade your plan') : __('Change plan') }}</h2>
    @include('partials.pricing', ['plans' => $plans, 'currentPlan' => $plan])

    <p class="mt-6 text-sm text-ink-subtle">{{ __('Payments are handled securely by Stripe. Prices are per month; you can cancel any time from Manage billing.') }}</p>
</x-layouts::app>
