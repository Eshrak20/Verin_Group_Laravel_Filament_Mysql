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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use App\Models\AttributeValue;
use App\Services\SkuGenerator;
use App\Services\SlugService;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ================= PRODUCT BASIC INFO =================

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

            Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

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
                ->required()
                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

            Select::make('brand_id')
                ->label('Brand')
                ->options(
                    fn(Get $get) =>
                    Brand::query()
                        ->where('sub_category_id', $get('sub_category_id'))
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

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
                ->columnSpanFull()
                ->schema([

                    TextInput::make('sku')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Auto-generated SKU')
                        ->formatStateUsing(function (Get $get, ?string $state) {
                            if ($state) return $state;

                            return SkuGenerator::generate(
                                $get('../../category_id'),
                                $get('../../sub_category_id'),
                                $get('../../brand_id'),
                                $get('attribute_values') ?? []
                            );
                        }),

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
                        ->relationship('attributeValues', 'value')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $sku = SkuGenerator::generate(
                                $get('../../category_id'),
                                $get('../../sub_category_id'),
                                $get('../../brand_id'),
                                $get('attribute_values') ?? []
                            );
                            $set('sku', $sku);
                        }),

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

                ]),
        ]);
    }

    /**
     * Correctly pulls the existing variants array via $get and recalculates SKUs
     */
    protected static function updateAllVariantSkus(Get $get, Set $set): void
    {
        // 1. Get the current active structural IDs from the root form state
        $categoryId = $get('category_id');
        $subCategoryId = $get('sub_category_id');
        $brandId = $get('brand_id');

        // 2. Fetch all currently open variant items inside the repeater
        $variants = $get('variants') ?? [];

        // 3. Loop through and replace the SKUs dynamically
        foreach ($variants as $key => $variant) {
            $variants[$key]['sku'] = SkuGenerator::generate(
                $categoryId,
                $subCategoryId,
                $brandId,
                $variant['attribute_values'] ?? []
            );
        }

        // 4. Push the mutated data structure safely back to Livewire
        $set('variants', $variants);
    }
}
