<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'branch_id',
        'product_variant_id',
        'stock',
        'reserved_stock',
        'low_stock_alert',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'product_variant_id' => 'integer',

        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'low_stock_alert' => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvailableStockAttribute(): int
    {
        return $this->stock - $this->reserved_stock;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->available_stock <= $this->low_stock_alert;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->available_stock <= 0;
    }
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
    
}
