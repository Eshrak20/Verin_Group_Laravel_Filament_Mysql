<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'category_id',
        'sub_category_id',
        'brand_id',
        'is_featured',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // ✅ ADD THIS (MISSING PART)
    public function attributes()
    {
        return $this->belongsToMany(
            Attribute::class,
            'product_attributes'
        );
    }

   
}
