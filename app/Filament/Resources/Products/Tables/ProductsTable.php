<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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

            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // DeleteAction::make(), // ✅ delete button added
            ]);

            // ->toolbarActions([
            //     BulkActionGroup::make([
            //         DeleteBulkAction::make(),
            //     ]),
            // ]);
    }
}