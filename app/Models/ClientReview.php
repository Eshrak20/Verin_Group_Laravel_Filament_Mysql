<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClientReview extends Model
{
    protected $fillable = [
        'client_name',
        'client_position',
        'client_image',
        'rating',
        'review',
        'item',
        'is_active',
        'sort_order',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : null;
    }
}
