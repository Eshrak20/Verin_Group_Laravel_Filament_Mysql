<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items'),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('BDT')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'cod' => 'Cash on Delivery',
                            'online' => 'Online',
                            default => ucfirst($state ?? 'Unknown'),
                        }
                    ),

                TextColumn::make('order_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn (?string $state): string => ucfirst($state ?? 'Unknown')
                    )
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'unpaid' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn (?string $state): string => ucfirst($state ?? 'Unknown')
                    ),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('order_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'cod' => 'Cash on Delivery',
                        'online' => 'Online',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->label('Manage'),

                Action::make('confirm')
                    ->label('Confirm Order')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm this order?')
                    ->visible(fn (Order $record): bool =>
                        $record->order_status === 'pending'
                    )
                    ->action(fn (Order $record) =>
                        $record->update([
                            'order_status' => 'confirmed',
                        ])
                    ),

                Action::make('cancel')
                    ->label('Cancel Order')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel this order?')
                    ->modalDescription(
                        'This will change the order status to cancelled.'
                    )
                    ->visible(fn (Order $record): bool =>
                        ! in_array(
                            $record->order_status,
                            ['cancelled', 'delivered'],
                            true
                        )
                    )
                    ->action(fn (Order $record) =>
                        $record->update([
                            'order_status' => 'cancelled',
                        ])
                    ),
            ])
            ->toolbarActions([]);
    }
}