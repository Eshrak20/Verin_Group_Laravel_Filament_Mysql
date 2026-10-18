<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[Fillable([
    'name',
    'email',
    'password',
    'otp',
    'attempts',
    'expired_at',
])]
#[Hidden(['password', 'otp'])]
class PendingUser extends Model
{
    use HasFactory, Notifiable;

    public function matchesOtp(string $otp): bool
    {
        return is_string($this->otp) && Hash::check($otp, $this->otp);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('expired_at', '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
