<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReview extends Model
{
    protected $fillable = [
        'client_name',
        'client_position',
        'client_image',
        'rating',
        'review',
        'item',
        'is_active',
        'sort_order',
    ];
}