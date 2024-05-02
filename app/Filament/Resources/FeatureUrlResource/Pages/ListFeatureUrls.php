<?php

namespace App\Filament\Resources\FeatureUrlResource\Pages;

use App\Filament\Resources\FeatureUrlResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureUrls extends ListRecords
{
    protected static string $resource = FeatureUrlResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
