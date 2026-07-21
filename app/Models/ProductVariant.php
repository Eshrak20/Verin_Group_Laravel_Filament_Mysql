<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'status',
    ];

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_value'
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function videos()
    {
        return $this->hasMany(ProductVariantVideo::class);
    }
}
