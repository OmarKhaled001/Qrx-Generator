<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class MonthlyStatusChart
{
    protected $QrStatus;

    public function __construct(LarapexChart $QrStatus)
    {
        $this->QrStatus = $QrStatus;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\DonutChart
    {
        return $this->QrStatus->donutChart()
            ->setTitle("Qrx's status")
            ->addData( [
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('status','active')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('status','pause')->count(),
                \App\Models\QrxCode::where('user_id', Auth()->user()->id)->where('status','exp')->count(),

            ])
            ->setColors(['#00E396', '#feb019', '#ff455f'])
            ->setLabels(['Active', 'Pause', 'Expire']);
    }
}
