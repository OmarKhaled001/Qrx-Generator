<?php

namespace App\Models;

use App\Enums\QrStatus;
use App\Enums\QrType;
use App\Support\QrDesign;
use App\Support\ShortCode;
use Database\Factories\QrCodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property QrType $type
 * @property QrStatus $status
 * @property array<string, mixed> $payload
 * @property array<string, mixed> $design
 */
#[Fillable(['name', 'type', 'payload', 'design', 'status'])]
class QrCode extends Model
{
    /** @use HasFactory<QrCodeFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'active',
        'scan_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'type' => QrType::class,
            'status' => QrStatus::class,
            'payload' => 'array',
            'design' => 'array',
            'scan_count' => 'integer',
            'last_scanned_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (QrCode $qrCode): void {
            $qrCode->code ??= ShortCode::generate();
            $qrCode->design = QrDesign::normalize($qrCode->design ?? []);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Scan, $this>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ownedBy(Builder $query, User $user): void
    {
        $query->where('user_id', $user->id);
    }

    public function shortUrl(): string
    {
        return url('/x/'.$this->code);
    }

    public function isPaused(): bool
    {
        return $this->status === QrStatus::Paused;
    }

    public function hasReachedScanLimit(Plan $plan): bool
    {
        return $this->scan_count >= $plan->scan_limit;
    }
}
