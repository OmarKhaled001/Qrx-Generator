<?php

namespace App\Filament\Resources\FeatureVcardResource\Pages;

use App\Filament\Resources\FeatureVcardResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureVcard extends EditRecord
{
    protected static string $resource = FeatureVcardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
