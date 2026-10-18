<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        ->default('pending')
                        ->required(),

                    Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'unpaid' => 'Unpaid',
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('unpaid')
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
                    TextInput::make('customer_name')
                        ->label('Customer Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Phone')
                        ->required()
                        ->tel()
                        ->maxLength(30),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->nullable()
                        ->maxLength(255),

                    Select::make('payment_method')
                        ->label('Payment Method')
                        ->options([
                            'cod' => 'Cash on Delivery',
                            'online' => 'Online Payment',
                        ])
                        ->required(),

                    Textarea::make('delivery_address')
                        ->label('Delivery Address')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('district')
                        ->label('District')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('area')
                        ->label('Area')
                        ->nullable()
                        ->maxLength(255),

                    Textarea::make('customer_note')
                        ->label('Customer Note')
                        ->nullable()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Order Summary')
                ->columns(2)
                ->schema([
                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->prefix('৳')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('delivery_charge')
                        ->label('Delivery Charge')
                        ->prefix('৳')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('discount_amount')
                        ->label('Discount Amount')
                        ->prefix('৳')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),

                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->prefix('৳')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),
                ]),
        ]);
    }
}