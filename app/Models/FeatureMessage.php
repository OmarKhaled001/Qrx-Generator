<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureMessage extends Model
{
    use HasFactory;
    protected $table = 'feature_messages';
    
    public $timestamps = true;

    protected $fillable = [
        'phone',
        'message',
        'qrx_code_id',

    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
