<?php

namespace App\Filament\Resources\FeaturePhoneResource\Pages;

use App\Filament\Resources\FeaturePhoneResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeaturePhone extends EditRecord
{
    protected static string $resource = FeaturePhoneResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
