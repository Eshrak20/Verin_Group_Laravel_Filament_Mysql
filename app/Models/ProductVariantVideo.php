<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ProductVariantVideo extends Model
{
    protected $fillable = [
        'product_variant_id',
        'video_provider',
        'video_url',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}