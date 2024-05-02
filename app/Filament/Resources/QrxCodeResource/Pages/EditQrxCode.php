<?php

namespace App\Filament\Resources\QrxCodeResource\Pages;

use App\Filament\Resources\QrxCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQrxCode extends EditRecord
{
    protected static string $resource = QrxCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
