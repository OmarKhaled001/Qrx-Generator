<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureLocation extends Model
{
    use HasFactory;
    protected $table = 'feature_locations';
    
    public $timestamps = true;

    protected $fillable = [
        'latitude',
        'longitude',
        'qr_code_id',

    ];

    public function qrCode( ){
        return $this->belongsTo(QrCode::class);
    }
}
