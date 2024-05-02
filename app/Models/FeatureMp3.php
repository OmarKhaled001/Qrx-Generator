<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeatureMp3 extends Model  implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    protected $table = 'feature_mp3s';
    
    public $timestamps = true;

    protected $fillable = [

        'qr_code_id',

    ];

    public function qrCode( ){
        return $this->belongsTo(QrxCode::class);
    }
}
