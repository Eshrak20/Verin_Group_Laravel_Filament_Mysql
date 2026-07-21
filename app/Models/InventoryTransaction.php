<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [

        'inventory_id',

        'type',

        'quantity',
        'from_inventory_id',

        'to_inventory_id',

        'before_stock',

        'after_stock',

        'remarks',

        'user_id',

    ];

    protected $casts = [

        'inventory_id' => 'integer',

        'quantity' => 'integer',

        'before_stock' => 'integer',

        'after_stock' => 'integer',

        'user_id' => 'integer',

    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function fromInventory()
    {
        return $this->belongsTo(
            Inventory::class,
            'from_inventory_id'
        );
    }


    public function toInventory()
    {
        return $this->belongsTo(
            Inventory::class,
            'to_inventory_id'
        );
    }
}
