<?php

namespace App\Filament\Widgets;

use App\Models\QrxCode;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\LineChartWidget;

class QrxCodeChart extends LineChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Qrx Codes';

    protected static ?string $pollingInterval = '10s';


    protected function getData(): array
    {

        $data = Trend::model(QrxCode::class)
        ->between(
            start: now()->startOfWeek(),
            end: now()->endOfWeek(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Qr Code',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                'borderColor'=> '#36A2EB',
                
            ],
        ],
        'labels' =>  ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri','Sat', 'Sun' ],
        
    ];
    }

}
