<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\QrxCode;
use App\Models\User;

class QrxCodePolicy
{
    /**
     * Filament admins (the `web` guard) keep full access to every QR code.
     */
    public function before($user): ?bool
    {
        return $user instanceof Admin ? true : null;
    }

    public function view(User $user, QrxCode $qrxCode): bool
    {
        return $this->owns($user, $qrxCode);
    }

    public function update(User $user, QrxCode $qrxCode): bool
    {
        return $this->owns($user, $qrxCode);
    }

    public function delete(User $user, QrxCode $qrxCode): bool
    {
        return $this->owns($user, $qrxCode);
    }

    private function owns(User $user, QrxCode $qrxCode): bool
    {
        return $qrxCode->user_id !== null && (int) $qrxCode->user_id === (int) $user->id;
    }
}
