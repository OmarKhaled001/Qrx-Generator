<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureVcard extends Model
{
    use HasFactory;
    protected $table = 'feature_vcards';
    
    public $timestamps = true;

    protected $fillable = [
        'full_name',
        'email',
        'tel',
        'url',
        'address',
        'note',
        'qrs_code_id',

    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
