<?php

namespace App\Filament\Resources\FeaturePhoneResource\Pages;

use App\Filament\Resources\FeaturePhoneResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeaturePhones extends ListRecords
{
    protected static string $resource = FeaturePhoneResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
