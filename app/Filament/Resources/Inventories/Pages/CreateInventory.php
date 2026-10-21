<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // This field is dehydrated(false), so read it from Livewire form state.
        $variantIds = $this->data['product_variant_ids'] ?? [];

        if (empty($variantIds)) {
            throw ValidationException::withMessages([
                'data.product_variant_ids' => 'Please select at least one product variant.',
            ]);
        }

        return DB::transaction(function () use ($data, $variantIds) {
            $firstInventory = null;

            foreach (array_unique($variantIds) as $variantId) {
                // Avoid creating duplicate inventory for the same branch and variant.
                $exists = Inventory::query()
                    ->where('branch_id', $data['branch_id'])
                    ->where('product_variant_id', $variantId)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'data.product_variant_ids' =>
                            "An inventory record already exists for variant ID {$variantId} in this branch.",
                    ]);
                }

                $inventory = Inventory::query()->create([
                    'branch_id' => $data['branch_id'],
                    'product_variant_id' => $variantId,
                    'stock' => $data['stock'] ?? 0,
                    'reserved_stock' => $data['reserved_stock'] ?? 0,
                    'low_stock_alert' => $data['low_stock_alert'] ?? 5,
                ]);

                $firstInventory ??= $inventory;
            }

            return $firstInventory;
        });
    }
}