<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * is_admin is not mass-assignable on purpose; set it explicitly. Accounts
     * created by an admin count as verified.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = new User(Arr::except($data, ['is_admin']));
        $user->forceFill(['is_admin' => (bool) ($data['is_admin'] ?? false), 'email_verified_at' => now()])->save();

        return $user;
    }
}
