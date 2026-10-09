<?php

namespace App\Policies;

use App\Models\QrCode;
use App\Models\User;

/**
 * Customers act only on their own QR codes; admins on all of them. Every
 * ability Filament checks is defined, because Laravel only consults before()
 * when the ability's method exists.
 */
class QrCodePolicy
{
    public function before(User $user): ?bool
    {
        return $user->is_admin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, QrCode $qrCode): bool
    {
        return $this->owns($user, $qrCode);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, QrCode $qrCode): bool
    {
        return $this->owns($user, $qrCode);
    }

    public function delete(User $user, QrCode $qrCode): bool
    {
        return $this->owns($user, $qrCode);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function owns(User $user, QrCode $qrCode): bool
    {
        return $qrCode->user_id === $user->id;
    }
}
