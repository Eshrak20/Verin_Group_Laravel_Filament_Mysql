<?php

namespace App\Filament\Resources\AttributeValues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Attribute;

class AttributeValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ✅ FIXED: dropdown instead of typing ID
                Select::make('attribute_id')
                    ->label('Attribute')
                    ->relationship('attribute', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('value')
                    ->label('Value (e.g. Red, XL, Blue)')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}