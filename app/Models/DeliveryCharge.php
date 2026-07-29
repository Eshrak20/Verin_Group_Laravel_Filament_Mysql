<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryCharge extends Model
{
    protected $fillable = [
        'name',
        'inside_dhaka_charge',
        'outside_dhaka_charge',
        'description',
        'status',
    ];

    protected $casts = [
        'inside_dhaka_charge' => 'decimal:2',
        'outside_dhaka_charge' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}