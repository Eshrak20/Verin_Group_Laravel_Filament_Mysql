<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantVideo extends Model
{
    protected $fillable = [
        'product_variant_id',
        'video',
        'video_url',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function videos()
    {
        return $this->hasMany(ProductVariantVideo::class);
    }
}
