<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship('subCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('icon')
                    ->default(null),
                FileUpload::make('image')
                    ->image(),
                Textarea::make('short_description')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
