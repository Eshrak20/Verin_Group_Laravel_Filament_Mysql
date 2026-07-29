<?php

namespace App\Filament\Resources\DeliveryCharges\Pages;

use App\Filament\Resources\DeliveryCharges\DeliveryChargeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryCharges extends ListRecords
{
    protected static string $resource = DeliveryChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
