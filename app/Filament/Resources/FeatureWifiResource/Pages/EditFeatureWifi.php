<?php

namespace App\Filament\Resources\FeatureWifiResource\Pages;

use App\Filament\Resources\FeatureWifiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureWifi extends EditRecord
{
    protected static string $resource = FeatureWifiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
