<?php

namespace App\Filament\Resources\FeatureVcardResource\Pages;

use App\Filament\Resources\FeatureVcardResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureVcards extends ListRecords
{
    protected static string $resource = FeatureVcardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
