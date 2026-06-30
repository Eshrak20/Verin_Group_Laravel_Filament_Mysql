<?php

namespace App\Filament\Resources\AttributeValues\Schemas;

use App\Models\AttributeValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('attribute_id')
                    ->label('Attribute')
                    ->relationship('attribute', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('value')
                    ->label('Value (e.g. Red, XL, Blue)')
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {

                                $exists = AttributeValue::query()
                                    ->where('attribute_id', $get('attribute_id'))
                                    ->where('value', $value)
                                    ->when(
                                        $record,
                                        fn ($query) => $query->whereKeyNot($record->id)
                                    )
                                    ->exists();

                                if ($exists) {
                                    $fail('This value already exists for the selected attribute.');
                                }
                            };
                        },
                    ]),
            ]);
    }
}