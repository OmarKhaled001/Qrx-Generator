<?php

namespace App\Filament\Resources\FeatureTextResource\Pages;

use App\Filament\Resources\FeatureTextResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureTexts extends ListRecords
{
    protected static string $resource = FeatureTextResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
