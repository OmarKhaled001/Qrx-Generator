<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Client;
use App\Models\QrxCode;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    
    protected function getCards(): array
    {
        //client
        $client = User::select('*')
        ->whereBetween('created_at', 
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->get();
        //Qr Code
        $qrx = QrxCode::select('*')
        ->whereBetween('created_at', 
            [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
        )->get();

        return [
            //client
            Card::make('Clients', User::count())
            ->description('New clients during the week'.' ' .$client->count())
            ->descriptionIcon('heroicon-s-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            //qr codes
            Card::make('Qr Codes', QrxCode::count())
            ->description('New qr code during the week'. ' '.$qrx->count())
            ->descriptionIcon('heroicon-s-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            //plans
            Card::make('Plans', Plan::count())
            ->description('Count of our plans')
            ->descriptionIcon('heroicon-s-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            //Transaction
            Card::make('Transaction', Transaction::sum('amount').' $')
            ->description('amount')
            ->descriptionIcon('heroicon-s-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
        ];
    }
}
