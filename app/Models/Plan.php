<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';
    
    public $timestamps = true;

    protected $fillable = [
        'plan_id',
        'price_id',
        'title',
        'price',
        'qr_count',
        'scan_count',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
    ];
    


}
