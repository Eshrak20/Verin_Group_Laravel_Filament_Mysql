<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('order_number')
                        ->label('Order Number')
                        ->copyable(),

                    TextEntry::make('created_at')
                        ->label('Order Date')
                        ->dateTime('d M Y, h:i A'),

                    TextEntry::make('order_status')
                        ->badge(),

                    TextEntry::make('payment_status')
                        ->label('Payment Status')
                        ->badge(),

                    TextEntry::make('payment_method')
                        ->label('Payment Method')
                        ->formatStateUsing(
                            fn (?string $state): string => match ($state) {
                                'cod' => 'Cash on Delivery',
                                'online' => 'Online',
                                default => ucfirst($state ?? 'Unknown'),
                            }
                        ),

                    TextEntry::make('payment_provider')
                        ->placeholder('Not applicable'),

                    TextEntry::make('transaction_id')
                        ->placeholder('Not available')
                        ->copyable(),
                ]),

            Section::make('Customer Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('customer_name')
                        ->label('Customer Name'),

                    TextEntry::make('phone')
                        ->copyable(),

                    TextEntry::make('email')
                        ->placeholder('Not provided'),

                    TextEntry::make('delivery_address')
                        ->columnSpanFull(),

                    TextEntry::make('district'),

                    TextEntry::make('area'),

                    TextEntry::make('customer_note')
                        ->placeholder('No customer note')
                        ->columnSpanFull(),
                ]),

            Section::make('Ordered Products')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            TextEntry::make('product_name')
                                ->label('Product'),

                            TextEntry::make('sku')
                                ->placeholder('N/A'),

                            TextEntry::make('quantity'),

                            TextEntry::make('unit_price')
                                ->label('Unit Price')
                                ->money('BDT'),

                            TextEntry::make('line_total')
                                ->label('Line Total')
                                ->money('BDT'),
                        ])
                        ->columns(2),
                ]),

            Section::make('Payment Summary')
                ->columns(2)
                ->schema([
                    TextEntry::make('subtotal')
                        ->money('BDT'),

                    TextEntry::make('delivery_charge')
                        ->label('Delivery Charge')
                        ->money('BDT'),

                    TextEntry::make('discount_amount')
                        ->label('Discount')
                        ->money('BDT'),

                    TextEntry::make('total_amount')
                        ->label('Grand Total')
                        ->money('BDT')
                        ->weight('bold'),
                ]),
        ]);
    }
}