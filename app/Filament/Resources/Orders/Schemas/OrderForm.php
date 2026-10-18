<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Status')
                ->columns(2)
                ->schema([
                    Select::make('order_status')
                        ->label('Order Status')
                        ->options([
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),

                    Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'unpaid' => 'Unpaid',
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->required(),

                ]),
            Section::make('Steadfast Courier')
                ->description('Manage the courier tracking number for this order.')
                ->schema([
                    TextInput::make('steadfast_tracking_number')
                        ->label('Steadfast Tracking Number')
                        ->placeholder('Enter tracking number')
                        ->maxLength(255)
                        ->nullable()
                        ->helperText(
                            'Leave empty until the order is booked with Steadfast Courier.'
                        )
                        ->columnSpanFull(),
                ]),
            Section::make('Customer Information')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\TextInput::make('customer_name')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('phone')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('email')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('payment_method')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\Textarea::make('delivery_address')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\TextInput::make('district')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('area')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\Textarea::make('customer_note')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Order Summary')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\TextInput::make('subtotal')
                        ->prefix('৳')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('delivery_charge')
                        ->prefix('৳')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('discount_amount')
                        ->prefix('৳')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('total_amount')
                        ->prefix('৳')
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ]);
    }
}
