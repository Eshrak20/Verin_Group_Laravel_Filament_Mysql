<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariantImage;
use App\Models\ProductVariantVideo;
use App\Models\Inventory;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_value',
            'product_variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }

    public function videos()
    {
        return $this->hasMany(ProductVariantVideo::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}