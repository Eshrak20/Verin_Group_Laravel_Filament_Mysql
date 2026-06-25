<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category'),

                TextColumn::make('subCategory.name')
                    ->label('Sub Category'),

                TextColumn::make('brand.name')
                    ->label('Brand'),

                IconColumn::make('is_featured')
                    ->boolean(),

                IconColumn::make('status')
                    ->boolean(),
            ])

            // This ensures the entire table filters update cleanly when values alter
            ->filters([
                // 1. Parent Category Filter
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                // 2. Dependent Sub-Category Filter
                SelectFilter::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship('subCategory', 'name', function (Builder $query) use ($table) {
                        $categoryData = $table->getFilter('category_id')?->getState();
                        $categoryId = $categoryData['value'] ?? null;

                        if ($categoryId) {
                            return $query->where('category_id', $categoryId);
                        }

                        return $query->whereNull('id');
                    })
                    ->searchable()
                    ->preload(),

                // 3. Dependent Brand Filter
                SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name', function (Builder $query) use ($table) {
                        $subCategoryData = $table->getFilter('sub_category_id')?->getState();
                        $subCategoryId = $subCategoryData['value'] ?? null;

                        if ($subCategoryId) {
                            return $query->where('sub_category_id', $subCategoryId);
                        }

                        return $query->whereNull('id');
                    })
                    ->searchable()
                    ->preload(),
            ]);
    }
}