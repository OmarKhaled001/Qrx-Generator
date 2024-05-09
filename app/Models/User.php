<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\QrxCode;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable ,Billable,InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'country',
        'city',
        'area',
        'is_active',
        'email',
        'password',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateCode() {
        
        $this->timestamps = false;
        $this->code = rand(1000,9999);
        $this->expire_at = now()->addMinute(15);
        $this->save();
        
    }

    public function destoryCode() {
        
        $this->timestamps = false;
        $this->code = null;
        $this->expire_at = null;
        $this->save();
        
    }

    public function qrxCodes( ){
        return $this->hasMany(QrxCode::class);
    }

        public function getJWTIdentifier() {
        return $this->getKey();
    }

    
    public function getJWTCustomClaims() {
        return [];
    }
    



}
