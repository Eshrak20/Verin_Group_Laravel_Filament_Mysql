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
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->client_image
            ? Storage::disk('public')->url($this->client_image)
            : null;
    }
}
