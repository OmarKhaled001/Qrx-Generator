<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

        public function qrxCodes( ){
        return $this->hasMany(QrxCode::class);
    }

    // Filament v2 rejects users without this outside APP_ENV=local.
    // Only the admins table authenticates on Filament's guard, so every admin may enter.
    public function canAccessFilament(): bool
    {
        return true;
    }

}