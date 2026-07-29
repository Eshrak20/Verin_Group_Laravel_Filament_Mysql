<?php

namespace App\Filament\Resources\DeliveryCharges;

use App\Filament\Resources\DeliveryCharges\Pages\CreateDeliveryCharge;
use App\Filament\Resources\DeliveryCharges\Pages\EditDeliveryCharge;
use App\Filament\Resources\DeliveryCharges\Pages\ListDeliveryCharges;
use App\Filament\Resources\DeliveryCharges\Schemas\DeliveryChargeForm;
use App\Filament\Resources\DeliveryCharges\Tables\DeliveryChargesTable;
use App\Models\DeliveryCharge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryChargeResource extends Resource
{
    protected static ?string $model = DeliveryCharge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeliveryChargeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryChargesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryCharges::route('/'),
            'create' => CreateDeliveryCharge::route('/create'),
            'edit' => EditDeliveryCharge::route('/{record}/edit'),
        ];
    }
}
