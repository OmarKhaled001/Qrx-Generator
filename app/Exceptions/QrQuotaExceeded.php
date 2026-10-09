<?php

namespace App\Exceptions;

use App\Models\Plan;
use RuntimeException;

final class QrQuotaExceeded extends RuntimeException
{
    public function __construct(public readonly Plan $plan)
    {
        parent::__construct("QR code limit of the [{$plan->slug}] plan reached.");
    }
}
