<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordChangeOtp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'new_password',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
