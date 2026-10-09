<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->hidden(fn () => $this->getRecord()->is(auth()->user())),
        ];
    }

    /**
     * is_admin is not mass-assignable on purpose; set it explicitly (and never
     * for your own account, whose toggle is disabled).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill(Arr::except($data, ['is_admin']));

        if (array_key_exists('is_admin', $data) && ! $record->is(auth()->user())) {
            $record->forceFill(['is_admin' => (bool) $data['is_admin']]);
        }

        $record->save();

        return $record;
    }
}
