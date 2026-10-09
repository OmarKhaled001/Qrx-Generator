<?php

namespace App\Models;

use Database\Factories\ScanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One scan of a QR code. Holds no raw IP address (see RecordScan).
 */
#[Fillable(['qr_code_id', 'scanned_at', 'device', 'referrer_host', 'country', 'visitor_hash'])]
class Scan extends Model
{
    /** @use HasFactory<ScanFactory> */
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<QrCode, $this>
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }
}
