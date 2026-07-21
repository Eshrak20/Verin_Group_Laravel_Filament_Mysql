<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Filament\Resources\Inventories\InventoryResource;
class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reserved_stock')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('low_stock_alert')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([

                Action::make('manage')

                    ->label('Manage')

                    ->icon('heroicon-o-cog-6-tooth')

                    ->url(
                        fn($record) =>
                        InventoryResource::getUrl('manage', [
                            'record' => $record,
                        ])
                    ),

            ])
            ->toolbarActions([]);
    }
}
