<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'page_name',
        'banner_image',
        'status',
        'is_slide',
        'sorting_number',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}