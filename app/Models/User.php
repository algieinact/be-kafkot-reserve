<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'username',
        'full_name',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Append name attribute to JSON (alias for full_name, for backward compatibility)
     */
    protected $appends = ['name'];

    /**
     * Get name attribute (alias for full_name)
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? '';
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'verified_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }
}
