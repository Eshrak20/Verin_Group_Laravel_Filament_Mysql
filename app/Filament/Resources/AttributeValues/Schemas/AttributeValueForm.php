<?php

namespace App\Filament\Resources\AttributeValues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Attribute;

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
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {

                        if (! $state) {
                            $set('values', []);
                            return;
                        }

                        $values = Attribute::find($state)
                            ?->values()
                            ->pluck('value')
                            ->toArray() ?? [];

                        $set('values', $values);
                    }),

                TagsInput::make('values')
                    ->afterStateHydrated(function ($component, $record) {
                        if (! $record) {
                            return;
                        }

                        $component->state(
                            $record->attribute
                                ->values()
                                ->pluck('value')
                                ->toArray()
                        );
                    }),
            ]);
    }
}
