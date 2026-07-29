<?php

namespace App\Filament\Resources\DeliveryCharges\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeliveryChargeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('inside_dhaka_charge')
                    ->required()
                    ->numeric(),
                TextInput::make('outside_dhaka_charge')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
