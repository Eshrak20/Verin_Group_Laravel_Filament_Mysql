<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([

                TextColumn::make('name')
                    ->label('Attribute')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('values')
                    ->label('Values')
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(fn($record) => $record->values()
                        ->pluck('value')
                        ->unique()
                        ->values()
                        ->all()),

                TextColumn::make('values_count')
                    ->label('Total')
                    ->counts('values')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
