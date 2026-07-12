<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TagsInput::make('values')
                    ->required()
                    ->label('Values')
                    ->placeholder('Type a value and press Enter')
                    ->afterStateHydrated(function ($component, $record) {
                        if (! $record) {
                            return;
                        }

                        $component->state(
                            $record->values()
                                ->pluck('value')
                                ->toArray()
                        );
                    })
            ]);
    }
}
