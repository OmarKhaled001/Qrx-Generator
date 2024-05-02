<?php

namespace App\Filament\Resources\FeatureWifiResource\Pages;

use App\Filament\Resources\FeatureWifiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureWifis extends ListRecords
{
    protected static string $resource = FeatureWifiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
