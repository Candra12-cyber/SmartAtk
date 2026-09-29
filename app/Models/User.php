<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'role',
        'username',
        'jabatan',
        'unit_kerja',
        'telepon',
        'foto',
        'ruangan',

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

    public function permintaans()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    

    /**
     * Cek apakah user adalah pegawai biasa.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }
}
