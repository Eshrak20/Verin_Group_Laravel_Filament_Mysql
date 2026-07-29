<?php

namespace App\Filament\Resources\DeliveryCharges\Pages;

use App\Filament\Resources\DeliveryCharges\DeliveryChargeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryCharge extends EditRecord
{
    protected static string $resource = DeliveryChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
