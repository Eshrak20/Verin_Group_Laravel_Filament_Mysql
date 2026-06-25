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

    // ✅ Variant images (1-to-many)
    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }

    // ✅ MANY-TO-MANY with attribute values
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_value'
        );
    }

    

    // optional (recommended)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function videos()
    {
        return $this->hasMany(ProductVariantVideo::class);
    }
}
