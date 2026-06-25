<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariantImage extends Model
{
    protected $fillable = [
        'product_variant_id',
        'image',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::disk('cloudinary')->url($this->image)
            : null;
    }
 
}
