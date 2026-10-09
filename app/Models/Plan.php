<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

#[Fillable([
    'name', 'slug', 'stripe_price_id', 'price_cents', 'currency', 'interval',
    'qr_limit', 'scan_limit', 'is_free', 'is_active', 'sort_order', 'features',
])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'qr_limit' => 'integer',
            'scan_limit' => 'integer',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'features' => 'array',
        ];
    }

    /**
     * The plan everyone without an active subscription is on. Falls back to the
     * configured limits if the free plan row has not been seeded.
     */
    public static function free(): self
    {
        return static::query()->where('is_free', true)->first()
            ?? new self([
                'name' => 'Free',
                'slug' => 'free',
                'qr_limit' => config('qrx.free_plan.qr_limit'),
                'scan_limit' => config('qrx.free_plan.scan_limit'),
                'is_free' => true,
                'is_active' => true,
            ]);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Active paid plans that can be bought through Stripe Checkout.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function purchasable(Builder $query): void
    {
        $query->where('is_active', true)->where('is_free', false)->whereNotNull('stripe_price_id');
    }

    public function isPurchasable(): bool
    {
        return $this->is_active && ! $this->is_free && filled($this->stripe_price_id);
    }

    public function formattedPrice(): string
    {
        return Number::currency($this->price_cents / 100, in: strtoupper($this->currency ?: 'usd'), locale: app()->getLocale(), precision: $this->price_cents % 100 === 0 ? 0 : 2);
    }
}
