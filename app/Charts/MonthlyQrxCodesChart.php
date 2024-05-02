<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class MonthlyQrxCodesChart
{
    protected $QrType;

    public function __construct(LarapexChart $QrType)
    {
        $this->QrType = $QrType;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\PieChart
    {


        return $this->QrType->pieChart()
            ->setTitle("Qrx's Type ")
            ->addData([
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','text')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','url')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','phone')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','messeage')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','email')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','vcard')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','wifi')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('type','location')->count(),
                ])
                ->setColors(['#008FFB', '#00E396', '#feb019', '#ff455f', '#775dd0', '#80effe','#0077B5', '#ff6384'])
            ->setLabels(['Text', 'Url', 'Phone', 'SMS', 'Email', 'Vcard', 'Wi-fi', 'Location']);
    }
}
