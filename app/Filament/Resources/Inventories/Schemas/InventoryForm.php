<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductVariant;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_filter')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id'))
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('sub_category_filter', null);
                        $set('brand_filter', null);
                        $set('product_variant_id', null);
                    }),

                Select::make('sub_category_filter')
                    ->label('Sub Category')
                    ->options(
                        fn(Get $get) => SubCategory::query()
                            ->where('category_id', $get('category_filter'))
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('brand_filter', null);
                        $set('product_variant_id', null);
                    }),

                Select::make('brand_filter')
                    ->label('Brand')
                    ->options(
                        fn(Get $get) => Brand::query()
                            ->where('sub_category_id', $get('sub_category_filter'))
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn(Set $set) => $set('product_variant_id', null)),
                Select::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),




                TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('reserved_stock')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('low_stock_alert')
                    ->numeric()
                    ->default(5)
                    ->required(),
                Select::make('product_variant_id')
                    ->searchable()
                    ->preload()
                    ->options(function (Get $get) {

                        return ProductVariant::query()
                            ->with('product')
                            ->whereHas('product', function ($query) use ($get) {

                                $query
                                    ->when(
                                        $get('category_filter'),
                                        fn($q, $id) => $q->where('category_id', $id)
                                    )
                                    ->when(
                                        $get('sub_category_filter'),
                                        fn($q, $id) => $q->where('sub_category_id', $id)
                                    )
                                    ->when(
                                        $get('brand_filter'),
                                        fn($q, $id) => $q->where('brand_id', $id)
                                    );
                            })
                            ->get()
                            ->mapWithKeys(fn($variant) => [
                                $variant->id => "{$variant->product->name} ({$variant->sku})",
                            ]);
                    })
            ]);
    }
}
