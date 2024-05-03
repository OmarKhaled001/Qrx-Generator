<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionItem extends Model
{
    use HasFactory;

    protected $table = 'subscription_items';
    
    public $timestamps = true;

    protected $fillable = [
        'subscription_id',
        'stripe_id',
        'stripe_product',
        'stripe_price',
        'quantity',

    ];

}
