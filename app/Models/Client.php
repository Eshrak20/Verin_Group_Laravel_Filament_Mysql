<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'is_active',
        'sort_order',
    ];
    protected $appends = ['image_url'];


    public function getImageUrlAttribute(): ?string
    {
        return $this->logo
            ? Storage::disk('public')->url($this->logo)
            : null;
    }
}
