<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturePhone extends Model
{
    use HasFactory;
    protected $table = 'feature_phones';
    
    public $timestamps = true;

    protected $fillable = [
        'phone',
        'qrx_code_id',

    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
