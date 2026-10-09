<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Revenue (from paid Stripe invoices) and sign-ups for the last six months.
 */
class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    public function getHeading(): string|Htmlable|null
    {
        return __('Revenue and sign-ups');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $ago) => now()->startOfMonth()->subMonths($ago));

        return [
            'datasets' => [
                [
                    'label' => __('Revenue (USD)'),
                    'data' => $months->map(fn ($month) => round(Payment::query()
                        ->whereBetween('paid_at', [$month, $month->copy()->endOfMonth()])
                        ->sum('amount_cents') / 100, 2))->all(),
                    'yAxisID' => 'y',
                ],
                [
                    'label' => __('Sign-ups'),
                    'type' => 'line',
                    'data' => $months->map(fn ($month) => User::query()
                        ->whereBetween('created_at', [$month, $month->copy()->endOfMonth()])
                        ->count())->all(),
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $months->map(fn ($month) => $month->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }
}
