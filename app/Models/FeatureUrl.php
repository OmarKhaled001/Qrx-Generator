<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureUrl extends Model
{
    use HasFactory;
    protected $table = 'feature_urls';
    
    public $timestamps = true;

    protected $fillable = [
        'url',
        'qrx_code_id',
    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
