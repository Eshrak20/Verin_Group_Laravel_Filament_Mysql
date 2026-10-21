<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Order overview
            Section::make('Order Overview')
                ->description('Order reference, current status, and timeline.')
                ->icon('heroicon-o-shopping-bag')
                ->columns(3)
                ->schema([
                    TextEntry::make('order_number')
                        ->label('Order Number')
                        ->icon('heroicon-o-hashtag')
                        ->weight('bold')
                        ->copyable()
                        ->copyMessage('Order number copied!')
                        ->copyMessageDuration(1500),

                    TextEntry::make('created_at')
                        ->label('Order Placed')
                        ->dateTime('d M Y, h:i A')
                        ->icon('heroicon-o-calendar'),

                    TextEntry::make('updated_at')
                        ->label('Last Updated')
                        ->dateTime('d M Y, h:i A')
                        ->since(),

                    TextEntry::make('order_status')
                        ->label('Order Status')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(
                            fn (?string $state): string => str($state ?? 'unknown')
                                ->replace('_', ' ')
                                ->title()
                                ->toString()
                        ),

                    TextEntry::make('payment_status')
                        ->label('Payment Status')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'paid' => 'success',
                            'pending' => 'warning',
                            'unpaid' => 'gray',
                            'failed' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(
                            fn (?string $state): string => str($state ?? 'unknown')
                                ->replace('_', ' ')
                                ->title()
                                ->toString()
                        ),

                    TextEntry::make('user.name')
                        ->label('Account')
                        ->placeholder('Guest / Admin-created')
                        ->icon('heroicon-o-user'),
                ]),

            // Courier information
            Section::make('Delivery & Tracking')
                ->description('Delivery destination and courier tracking details.')
                ->icon('heroicon-o-truck')
                ->columns(2)
                ->schema([
                    TextEntry::make('steadfast_tracking_number')
                        ->label('Steadfast Tracking Number')
                        ->placeholder('Not assigned yet')
                        ->icon('heroicon-o-qr-code')
                        ->weight('bold')
                        ->copyable()
                        ->copyMessage('Tracking number copied!')
                        ->copyMessageDuration(1500),

                    TextEntry::make('delivery_zone')
                        ->label('Delivery Zone')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(
                            fn (?string $state): string => match ($state) {
                                'inside_dhaka' => 'Inside Dhaka',
                                'outside_dhaka' => 'Outside Dhaka',
                                default => str($state ?? 'Not specified')
                                    ->replace('_', ' ')
                                    ->title()
                                    ->toString(),
                            }
                        ),

                    TextEntry::make('delivery_address')
                        ->label('Full Address')
                        ->icon('heroicon-o-map-pin')
                        ->copyable()
                        ->copyMessage('Delivery address copied!')
                        ->copyMessageDuration(1500)
                        ->columnSpanFull(),

                    TextEntry::make('district')
                        ->label('District')
                        ->placeholder('Not provided'),

                    TextEntry::make('area')
                        ->label('Area / Neighborhood')
                        ->placeholder('Not provided'),
                ]),

            // Customer information
            Section::make('Customer Information')
                ->description('Contact information for this order.')
                ->icon('heroicon-o-identification')
                ->columns(2)
                ->schema([
                    TextEntry::make('customer_name')
                        ->label('Customer Name')
                        ->icon('heroicon-o-user')
                        ->weight('medium')
                        ->copyable()
                        ->copyMessage('Customer name copied!'),

                    TextEntry::make('phone')
                        ->label('Phone Number')
                        ->icon('heroicon-o-phone')
                        ->copyable()
                        ->copyMessage('Phone number copied!')
                        ->copyMessageDuration(1500),

                    TextEntry::make('email')
                        ->label('Email Address')
                        ->icon('heroicon-o-envelope')
                        ->placeholder('Not provided')
                        ->copyable()
                        ->copyMessage('Email copied!')
                        ->copyMessageDuration(1500),

                    TextEntry::make('customer_note')
                        ->label('Customer Note')
                        ->placeholder('No customer note')
                        ->columnSpanFull(),
                ]),

            // Payment information
            Section::make('Payment Information')
                ->description('Payment method and transaction reference.')
                ->icon('heroicon-o-credit-card')
                ->columns(2)
                ->schema([
                    TextEntry::make('payment_method')
                        ->label('Payment Method')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'cod' => 'warning',
                            'online' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(
                            fn (?string $state): string => match ($state) {
                                'cod' => 'Cash on Delivery',
                                'online' => 'Online Payment',
                                default => str($state ?? 'Unknown')
                                    ->replace('_', ' ')
                                    ->title()
                                    ->toString(),
                            }
                        ),

                    TextEntry::make('payment_provider')
                        ->label('Payment Provider')
                        ->placeholder('Not applicable')
                        ->formatStateUsing(
                            fn (?string $state): string => match ($state) {
                                'bkash' => 'bKash',
                                'nagad' => 'Nagad',
                                'rocket' => 'Rocket',
                                'sslcommerz' => 'SSLCOMMERZ',
                                default => str($state ?? 'Not applicable')
                                    ->replace('_', ' ')
                                    ->title()
                                    ->toString(),
                            }
                        ),

                    TextEntry::make('transaction_id')
                        ->label('Transaction ID')
                        ->placeholder('No transaction ID')
                        ->copyable()
                        ->copyMessage('Transaction ID copied!')
                        ->copyMessageDuration(1500)
                        ->columnSpanFull(),
                ]),

            // Products and variants
            Section::make('Ordered Products')
                ->description('Product variants, quantities, prices, and discounts saved with this order.')
                ->icon('heroicon-o-cube')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('Order Items')
                        ->contained()
                        ->schema([
                            TextEntry::make('product_name')
                                ->label('Product')
                                ->weight('bold')
                                ->placeholder('Product name unavailable'),

                            TextEntry::make('sku')
                                ->label('Variant SKU')
                                ->icon('heroicon-o-qr-code')
                                ->placeholder('No SKU')
                                ->copyable()
                                ->copyMessage('SKU copied!')
                                ->copyMessageDuration(1500),

                            TextEntry::make('product_variant_id')
                                ->label('Variant ID')
                                ->copyable()
                                ->placeholder('N/A'),

                            TextEntry::make('quantity')
                                ->label('Quantity')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('regular_unit_price')
                                ->label('Regular Unit Price')
                                ->money('BDT')
                                ->placeholder('Not recorded'),

                            TextEntry::make('unit_price')
                                ->label('Selling Unit Price')
                                ->money('BDT')
                                ->weight('medium'),

                            TextEntry::make('discount_amount')
                                ->label('Item Discount')
                                ->money('BDT')
                                ->color('success')
                                ->placeholder('৳0.00'),

                            TextEntry::make('delivery_charge')
                                ->label('Item Delivery Charge')
                                ->money('BDT')
                                ->placeholder('৳0.00'),

                            TextEntry::make('line_total')
                                ->label('Line Total')
                                ->money('BDT')
                                ->weight('bold')
                                ->color('success'),
                        ])
                        ->columns(3),
                ]),

            // Financial summary
            Section::make('Payment Summary')
                ->description('Final order totals recorded at checkout.')
                ->icon('heroicon-o-banknotes')
                ->columns(2)
                ->schema([
                    TextEntry::make('subtotal')
                        ->label('Subtotal (After Item Discounts)')
                        ->money('BDT')
                        ->weight('medium'),

                    TextEntry::make('discount_amount')
                        ->label('Total Product Discounts')
                        ->money('BDT')
                        ->color('success'),

                    TextEntry::make('delivery_charge')
                        ->label('Total Delivery Charge')
                        ->money('BDT'),

                    TextEntry::make('total_amount')
                        ->label('Grand Total')
                        ->money('BDT')
                        ->weight('bold')
                        ->size('lg')
                        ->color('success'),
                ]),
        ]);
    }
}
