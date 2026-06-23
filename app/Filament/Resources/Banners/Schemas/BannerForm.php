<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page_name')
                    ->label('Page Name')
                    ->options([
                        'home' => 'Home',
                        'decor' => 'Decor',
                        'electronics' => 'Electronics',
                        'logistic' => 'Logistic',
                        'clothing' => 'Clothing',
                    ])
                    ->searchable()
                    ->required(),

                FileUpload::make('banner_image')
                    ->image()
                    ->directory('banners')
                    ->required(),

                Toggle::make('status')
                    ->default(true),
                Toggle::make('is_slide')
                    ->label('Is Slide')
                    ->default(false),
                TextInput::make('sorting_number')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
