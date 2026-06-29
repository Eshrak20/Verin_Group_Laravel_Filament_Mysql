<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Models\Category;
use App\Models\SubCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('subCategory.name')
                    ->label('Sub Category')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('subCategory.category.name')
                    ->label('Category')
                    ->searchable()
                    ->badge()
                    ->color('danger'),

                TextColumn::make('icon')
                    ->searchable(),

                ImageColumn::make('image')
                    ->disk('public')
                    ->visibility('public'),

                IconColumn::make('status')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('category_and_subcategory')
                    ->form([
                        // 1. Dynamic Category Dropdown
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::pluck('name', 'id')->toArray())
                            ->live() // Tells Filament to update the form layout instantly when clicked
                            ->selectablePlaceholder(true), // Keeps the default fallback "All" state active

                        // 2. Dependent Sub-Category Dropdown
                        Select::make('sub_category_id')
                            ->label('Sub Category')
                            ->options(function (callable $get) {
                                $categoryId = $get('category_id');

                                // Fallback: If no parent Category is chosen, load all Sub Categories
                                if (! $categoryId) {
                                    return SubCategory::pluck('name', 'id')->toArray();
                                }

                                // Reactive logic: Filter options matching only the selected parent Category
                                return SubCategory::where('category_id', $categoryId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->selectablePlaceholder(true),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            // Apply Category filter scope
                            ->when(
                                $data['category_id'],
                                fn(Builder $query, $value): Builder => $query->whereHas(
                                    'subCategory.category',
                                    fn(Builder $query) => $query->where('id', $value)
                                )
                            )
                            // Apply Sub-Category filter scope
                            ->when(
                                $data['sub_category_id'],
                                fn(Builder $query, $value): Builder => $query->where('sub_category_id', $value)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
