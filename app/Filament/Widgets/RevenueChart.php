<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use App\Support\Locales;
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
                    'backgroundColor' => '#00a5ab',
                    'borderRadius' => 6,
                    'maxBarThickness' => 40,
                ],
                [
                    'label' => __('Sign-ups'),
                    'type' => 'line',
                    'data' => $months->map(fn ($month) => User::query()
                        ->whereBetween('created_at', [$month, $month->copy()->endOfMonth()])
                        ->count())->all(),
                    'yAxisID' => 'y1',
                    'borderColor' => '#7543e9',
                    'backgroundColor' => '#7543e9',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn ($month) => $month->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        // In Arabic, time runs right to left and the axes swap sides, like the client dashboard chart.
        $rtl = Locales::isRtl();

        return [
            'scales' => [
                'x' => ['reverse' => $rtl, 'grid' => ['display' => false]],
                'y' => ['beginAtZero' => true, 'position' => $rtl ? 'right' : 'left'],
                'y1' => ['beginAtZero' => true, 'position' => $rtl ? 'left' : 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
            'plugins' => [
                'legend' => ['rtl' => $rtl, 'labels' => ['usePointStyle' => true]],
                'tooltip' => ['rtl' => $rtl],
            ],
        ];
    }
}
