<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureText extends Model
{
    use HasFactory;
    protected $table = 'feature_texts';
    
    public $timestamps = true;

    protected $fillable = [
        'text',
        'qrx_code_id',
    ];
    
    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }


}
