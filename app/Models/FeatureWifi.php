<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureWifi extends Model
{
    use HasFactory;
    protected $table = 'feature_wifis';
    
    public $timestamps = true;

    protected $fillable = [
        'wireless_ssid',
        'password',
        'encryption',
        'qrx_code_id',

    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
