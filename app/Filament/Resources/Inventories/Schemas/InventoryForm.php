<?php

namespace App\Filament\Resources\Inventories\Schemas;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_filter')
                    ->label('Category')
                    ->options(fn () => Category::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('sub_category_filter', null);
                        $set('brand_filter', null);
                        $set('product_variant_ids', []);
                    }),

                Select::make('sub_category_filter')
                    ->label('Sub Category')
                    ->options(fn (Get $get) => $get('category_filter')
                        ? SubCategory::query()
                            ->where('category_id', $get('category_filter'))
                            ->pluck('name', 'id')
                        : [])
                    ->required()
                    ->searchable()
                    ->live()
                    ->disabled(fn (Get $get) => ! $get('category_filter'))
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('brand_filter', null);
                        $set('product_variant_ids', []);
                    }),

                Select::make('brand_filter')
                    ->label('Brand (Optional)')
                    ->options(fn (Get $get) => $get('sub_category_filter')
                        ? Brand::query()
                            ->where('sub_category_id', $get('sub_category_filter'))
                            ->pluck('name', 'id')
                        : [])
                    ->searchable()
                    ->live()
                    ->disabled(fn (Get $get) => ! $get('sub_category_filter'))
                    ->dehydrated(false)
                    ->afterStateUpdated(
                        fn (Set $set) => $set('product_variant_ids', [])
                    ),

                Select::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('product_variant_ids')
                    ->label('Product Variants')
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->live()
                    ->dehydrated(false)
                    ->disabled(fn (Get $get) => ! $get('sub_category_filter'))
                    ->options(function (Get $get) {
                        $subCategoryId = $get('sub_category_filter');

                        if (! $subCategoryId) {
                            return [];
                        }

                        return ProductVariant::query()
                            ->with('product')
                            ->whereHas('product', function ($query) use ($get, $subCategoryId) {
                                $query
                                    ->where('category_id', $get('category_filter'))
                                    ->where('sub_category_id', $subCategoryId)
                                    ->when(
                                        $get('brand_filter'),
                                        fn ($q, $brandId) => $q->where('brand_id', $brandId)
                                    );
                            })
                            ->get()
                            ->mapWithKeys(fn ($variant) => [
                                $variant->id => "{$variant->product->name} ({$variant->sku})",
                            ])
                            ->all();
                    })
                    ->columnSpanFull(),

                TextInput::make('stock')
                    ->label('Initial Stock')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('reserved_stock')
                    ->label('Reserved Stock')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('low_stock_alert')
                    ->label('Low Stock Alert')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(5)
                    ->required(),
            ]);
    }
}