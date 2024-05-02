<?php

namespace App\Filament\Resources\FeatureUrlResource\Pages;

use App\Filament\Resources\FeatureUrlResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureUrl extends EditRecord
{
    protected static string $resource = FeatureUrlResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
