<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\SubCategory;
use App\Models\Brand;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use App\Models\AttributeValue;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ================= PRODUCT BASIC INFO =================

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->live()
                ->required(),

            Select::make('sub_category_id')
                ->label('Sub Category')
                ->options(
                    fn(Get $get) =>
                    SubCategory::query()
                        ->where('category_id', $get('category_id'))
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->live()
                ->required(),

            Select::make('brand_id')
                ->label('Brand')
                ->options(
                    fn(Get $get) =>
                    Brand::query()
                        ->where('sub_category_id', $get('sub_category_id'))
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->required(),

            /* ✅ ADD HERE */
            Select::make('attributes')
                ->label('Product Options (Color, Size etc.)')
                ->multiple()
                ->relationship('attributes', 'name')
                ->preload()
                ->searchable(),

            Textarea::make('short_description')
                ->columnSpanFull(),

            Textarea::make('description')
                ->columnSpanFull(),

            TextInput::make('thumbnail')
                ->label('Product Thumbnail (Cloudinary URL)')
                ->maxLength(255),

            Toggle::make('is_featured')
                ->default(false),

            Toggle::make('status')
                ->default(true),

            // ================= VARIANTS =================

            Repeater::make('variants')
                ->relationship()
                ->label('Product Variants')
                ->schema([

                    TextInput::make('sku')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('price')
                        ->numeric()
                        ->required(),

                    TextInput::make('sale_price')
                        ->numeric(),

                    Toggle::make('status')
                        ->default(true),

                    // ================= ATTRIBUTE OPTIONS =================

                    Select::make('attribute_values')
                        ->label('Options (Color / Size etc.)')
                        ->multiple()
                        ->searchable()
                        ->options(
                            AttributeValue::all()->pluck('value', 'id')
                        )
                        ->relationship('attributeValues', 'value'),

                    // ================= VARIANT IMAGES =================
                    // ================= VARIANT IMAGES =================

                    // ================= VARIANT IMAGES =================

                    // ================= VARIANT IMAGES =================

                    Repeater::make('images')
                        ->relationship('images')
                        ->label('Variant Images')
                        ->columnSpanFull()
                        ->schema([
                            FileUpload::make('image')
                                ->label('Image')
                                ->disk('cloudinary')
                                ->image()
                                ->required()
                                ->reorderable(),
                        ]),

                ])
                ->columnSpanFull(),
        ]);
    }
}
