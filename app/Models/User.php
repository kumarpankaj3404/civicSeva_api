<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'aadhaar_hash',
        'profile_completed',
        'avatar',
        'state',
        'district',
        'date_of_birth',
        'gender',
        'annual_income',
        'caste_category',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'aadhaar_hash',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'profile_completed' => 'boolean',
            'date_of_birth'     => 'date',
            'annual_income'     => 'integer',
            'preferences'       => 'array',
        ];
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
