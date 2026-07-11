<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
    
}