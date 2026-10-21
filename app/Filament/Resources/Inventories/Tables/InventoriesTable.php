<?php

namespace App\Filament\Resources\Inventories\Tables;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                    ->label('Total Stock')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reserved_stock')
                    ->label('Reserved')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('available_stock')
                    ->label('Available Stock')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(query: fn ($query, string $direction) => $query
                        ->orderByRaw(
                            '(stock - reserved_stock) ' . $direction
                        )),

                TextColumn::make('low_stock_alert')
                    ->label('Low Stock Alert')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn ($record) => InventoryResource::getUrl('manage', [
                        'record' => $record,
                    ])),
            ])
            ->toolbarActions([]);
    }
}