<?php

namespace App\Filament\Resources\FeatureTextResource\Pages;

use App\Filament\Resources\FeatureTextResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureText extends EditRecord
{
    protected static string $resource = FeatureTextResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
