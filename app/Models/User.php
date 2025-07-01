<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'nik', 'phone', 'address', 'job', 'birth_date', 'password', 'role', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function processedComplaints()
    {
        return $this->hasMany(Complaint::class, 'processed_by');
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

}
