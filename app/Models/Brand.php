<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $fillable = [
        'sub_category_id',
        'name',
        'slug',
        'icon',
        'image',
        'short_description',
        'status',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
