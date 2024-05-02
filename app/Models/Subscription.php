<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'type',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'trial_ends_at',
        'quantity',
        'ends_at',
    ];

    public function user( ){
        return $this->belongsTo(User::class);
    }

    public function items( ){
        return $this->hasMany(SubscriptionItem::class);
    }

}
