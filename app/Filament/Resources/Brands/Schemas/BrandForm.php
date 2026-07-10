<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\SlugService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false) // Don't save this field
                    ->afterStateUpdated(fn(Set $set) => $set('sub_category_id', null))
                    ->afterStateHydrated(function (Set $set, $record) {
                        if ($record?->subCategory) {
                            $set('category_id', $record->subCategory->category_id);
                        }
                    })
                    ->required(),

                Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship(
                        name: 'subCategory',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn($query, Get $get) =>
                        $query->when(
                            $get('category_id'),
                            fn($q, $categoryId) => $q->where('category_id', $categoryId)
                        )
                    )
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

                TextInput::make('icon'),

                FileUpload::make('image')
                    ->disk('public')
                    ->directory('brands'),

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
