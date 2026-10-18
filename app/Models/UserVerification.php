<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

#[Fillable([
    'user_id',
    'email',
    'otp',
    'purpose',
    'attempts',
    'expired_at',
])]
class UserVerification extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at->isPast();
    }

    public function matches(string $otp): bool
    {
        return Hash::check($otp, $this->otp);
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
        ];
    }
}
