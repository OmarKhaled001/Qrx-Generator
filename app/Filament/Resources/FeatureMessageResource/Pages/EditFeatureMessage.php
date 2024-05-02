<?php

namespace App\Filament\Resources\FeatureMessageResource\Pages;

use App\Filament\Resources\FeatureMessageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureMessage extends EditRecord
{
    protected static string $resource = FeatureMessageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
