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
        'type',
        'title',
        'details',
        'price',
        'duration',
        'scans_count',
        'qrs_count',
        'is_active',
        'is_hidden'
    ];
    


}
