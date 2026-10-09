<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\QrCode;
use App\Models\Scan;
use App\Models\User;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Laravel\Cashier\Subscription;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Four cheap counts: render with the page instead of a second request.
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $since = now()->subDays(30);

        return [
            Stat::make(__('Users'), Number::format(User::query()->count()))
                ->description(__(':count new in the last 30 days', ['count' => Number::format(User::query()->where('created_at', '>=', $since)->count())])),
            Stat::make(__('Paying subscribers'), Number::format(Subscription::query()->whereIn('stripe_status', ['active', 'trialing', 'past_due'])->count())),
            Stat::make(__('Revenue (30 days)'), Money::format((int) Payment::query()->where('paid_at', '>=', $since)->sum('amount_cents'))),
            Stat::make(__('QR codes'), Number::format(QrCode::query()->count())),
            Stat::make(__('Scans (30 days)'), Number::format(Scan::query()->where('scanned_at', '>=', $since)->count())),
        ];
    }
}
