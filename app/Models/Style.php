<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    use HasFactory;
    
    protected $table = 'styles';
    
    public $timestamps = true;

    protected $fillable = [
        'size',
        'style',
        'color',
        'bg_color',
        'eye',
        'margin',
        'gradient_from',
        'gradient_to',
        'qrx_code_id',
    ];

    public function qrxCode( ){
        return $this->belongsTo(QrxCode::class);
    }

}
