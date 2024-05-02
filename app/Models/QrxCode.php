<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class QrxCode extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $table = 'qrx_codes';
    
    public $timestamps = true;

    protected $fillable = [
        'name',
        'code',
        'scan_count',
        'status',
        'path',
        'client_id',
    ];
    
    public function user( ){
        return $this->belongsTo(User::class);
    }
    public function style( ){
        return $this->hasOne(Style::class);
    }
    public function text( ) {
        return $this->hasOne(FeatureText::class);
    }
    public function url( ){
        return $this->hasOne(FeatureUrl::class);
    }
    public function email( ){
        return $this->hasOne(FeatureEmail::class);
    }
    public function message( ){
        return $this->hasOne(FeatureMessage::class);
    }
    public function phone( ){
        return $this->hasOne(FeaturePhone::class);
    }
    public function vcard( ){
        return $this->hasOne(FeatureVcard::class);
    }
    public function wifi( ){
        return $this->hasOne(FeatureWifi::class);
    }



    public function generateCode() {
        $this->code = 'qx-'.rand(100000,999999);
        $this->save();
    }
}
