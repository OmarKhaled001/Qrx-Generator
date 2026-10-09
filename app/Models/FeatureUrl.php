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

    // The URL only when it is safe to use as a link target (http/https), otherwise null.
    public function safeHref(): ?string
    {
        $scheme = strtolower((string) parse_url(trim((string) $this->url), PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? trim($this->url) : null;
    }
}
