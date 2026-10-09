<?php

namespace App\Actions\Qr;

use App\Enums\QrType;
use App\Exceptions\QrQuotaExceeded;
use App\Models\QrCode;
use App\Models\User;
use App\Services\Subscriptions;
use Illuminate\Support\Facades\DB;

final class CreateQrCode
{
    public function __construct(private readonly Subscriptions $subscriptions) {}

    /**
     * @param  array<string, mixed>  $payload  already validated against $type->rules()
     * @param  array<string, mixed>  $design  already validated against QrDesign::rules()
     *
     * @throws QrQuotaExceeded
     */
    public function handle(User $user, string $name, QrType $type, array $payload, array $design): QrCode
    {
        return DB::transaction(function () use ($user, $name, $type, $payload, $design): QrCode {
            // Serialise concurrent creates for the same user so the quota can't be overshot.
            User::query()->whereKey($user->getKey())->lockForUpdate()->first();

            if (! $this->subscriptions->canCreateQrCode($user)) {
                throw new QrQuotaExceeded($this->subscriptions->currentPlan($user));
            }

            return $user->qrCodes()->create([
                'name' => $name,
                'type' => $type,
                'payload' => $type->clean($payload),
                'design' => $design,
            ]);
        });
    }
}
