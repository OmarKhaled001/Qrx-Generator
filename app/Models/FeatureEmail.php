<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureEmail extends Model
{
    use HasFactory;

    protected $table = 'feature_emails';
    
    public $timestamps = true;

    protected $fillable = [
        'email',
        'subject',
        'body',
        'qrx_code_id',
    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
