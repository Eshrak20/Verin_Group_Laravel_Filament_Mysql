<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('domain')
                    ->default(null),
                TextInput::make('logo')
                    ->default(null),
                TextInput::make('favicon')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
