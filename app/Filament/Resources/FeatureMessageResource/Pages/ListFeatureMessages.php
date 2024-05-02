<?php

namespace App\Filament\Resources\FeatureMessageResource\Pages;

use App\Filament\Resources\FeatureMessageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureMessages extends ListRecords
{
    protected static string $resource = FeatureMessageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
