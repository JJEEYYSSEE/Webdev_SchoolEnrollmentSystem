<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'birthdate',
        'school_id',
        'password',
        'must_change_password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthdate' => 'date',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    /** A bona fide student has been issued a School ID at admission. */
    public function isAdmitted(): bool
    {
        return ! is_null($this->school_id);
    }

    public function registrar()
    {
        return $this->hasOne(Registrar::class);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isRegistrar(): bool
    {
        return $this->role === 'registrar';
    }
}
