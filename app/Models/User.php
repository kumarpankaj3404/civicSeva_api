<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'aadhaar_hash',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'profile_completed'  => 'boolean',
            'date_of_birth'      => 'date',
            'annual_income'      => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
