<?php

namespace App\Filament\Resources\FeatureEmailResource\Pages;

use App\Filament\Resources\FeatureEmailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureEmail extends EditRecord
{
    protected static string $resource = FeatureEmailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
