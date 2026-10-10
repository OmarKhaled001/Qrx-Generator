{{-- Pricing cards plus a feature comparison built from each plan's real limits and capabilities. --}}
@php
    $rows = [
        __('Dynamic QR codes') => fn ($plan) => Number::format($plan->qr_limit),
        __('Scans per QR code') => fn ($plan) => Number::format($plan->scan_limit),
        __('All 8 QR types') => fn ($plan) => true,
        __('Edit content after printing') => fn ($plan) => true,
        __('Colours, gradients and styles') => fn ($plan) => true,
        __('PNG and SVG downloads') => fn ($plan) => true,
        __('EPS for professional print') => fn ($plan) => ! $plan->is_free,
        __('Scan analytics') => fn ($plan) => ! $plan->is_free,
        __('Priority support') => fn ($plan) => in_array('Priority support', $plan->features ?? [], true),
    ];
@endphp
<section id="pricing" class="border-y border-line bg-sunken/50">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-28">
        <x-landing.heading center :eyebrow="__('Pricing')" :title="__('Simple pricing')" :description="__('Start free. Upgrade when you need more codes or scans. Cancel any time.')" />

        <div class="mt-14">
            @include('partials.pricing', ['plans' => $plans, 'currentPlan' => $currentPlan])
        </div>

        <div class="mt-16">
            <h3 class="text-center text-xl font-semibold text-ink">{{ __('Compare plans') }}</h3>
            {{-- relative: the sr-only cell labels are absolutely positioned and must be clipped by this scroller. --}}
            <div class="relative mt-6 overflow-x-auto rounded-2xl border border-line bg-surface shadow-card">
                <table class="w-full min-w-[40rem] text-sm">
                    <caption class="sr-only">{{ __('Compare plans') }}</caption>
                    <thead>
                        <tr class="border-b border-line">
                            <th scope="col" class="sticky start-0 bg-surface px-5 py-4 text-start font-semibold text-ink-muted">{{ __('Feature') }}</th>
                            @foreach ($plans as $plan)
                                <th scope="col" @class(['px-4 py-4 text-center font-semibold', 'text-primary-ink' => $plan->slug === 'business', 'text-ink' => $plan->slug !== 'business'])>{{ __($plan->name) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($rows as $label => $value)
                            <tr class="transition-colors hover:bg-sunken/50">
                                <th scope="row" class="sticky start-0 bg-surface px-5 py-3.5 text-start font-medium text-ink">{{ $label }}</th>
                                @foreach ($plans as $plan)
                                    @php($cell = $value($plan))
                                    <td class="px-4 py-3.5 text-center tabular-nums">
                                        @if ($cell === true)
                                            <x-heroicon-m-check-circle class="mx-auto size-5 text-success-600" aria-hidden="true" /><span class="sr-only">{{ __('Included') }}</span>
                                        @elseif ($cell === false)
                                            <x-heroicon-m-minus class="mx-auto size-5 text-ink-subtle/60" aria-hidden="true" /><span class="sr-only">{{ __('Not included') }}</span>
                                        @else
                                            <span class="font-medium text-ink">{{ $cell }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
