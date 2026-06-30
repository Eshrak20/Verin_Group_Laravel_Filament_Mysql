<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use App\Services\SlugService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;

class SubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $context) {
                        SlugService::generate($set, $state, $context);
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('icon')
                    ->default(null),
                FileUpload::make('image')
                    ->disk('public')
                    ->directory('subcategories'),
                RichEditor::make('short_description')
                    ->label('Short Description')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 100px;',
                    ]),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
