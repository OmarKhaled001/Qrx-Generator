<?php

namespace App\Actions\Qr;

use App\Enums\QrType;
use App\Models\QrCode;
use App\Support\QrDesign;

final class UpdateQrCode
{
    /**
     * Changing the type replaces the payload entirely; there are no per-type
     * rows left behind (the old app's orphaned feature rows, audit M2).
     *
     * @param  array<string, mixed>  $payload  already validated against $type->rules()
     * @param  array<string, mixed>  $design  already validated against QrDesign::rules()
     */
    public function handle(QrCode $qrCode, string $name, QrType $type, array $payload, array $design): QrCode
    {
        $qrCode->update([
            'name' => $name,
            'type' => $type,
            'payload' => $type->clean($payload),
            'design' => QrDesign::normalize($design),
        ]);

        return $qrCode;
    }
}
