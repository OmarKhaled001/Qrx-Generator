<?php

namespace App\Filament\Resources\QrxCodeResource\Pages;

use App\Filament\Resources\QrxCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrxCodes extends ListRecords
{
    protected static string $resource = QrxCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
