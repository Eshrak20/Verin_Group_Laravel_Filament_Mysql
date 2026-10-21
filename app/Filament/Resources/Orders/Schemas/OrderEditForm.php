<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class OrderEditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Status')
                ->description('Update the current order fulfillment status.')
                ->icon('heroicon-o-shopping-bag')
                ->columns(2)
                ->schema([
                    Select::make('order_status')
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
                        ->options([
                            'unpaid' => 'Unpaid',
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->required(),
                ]),

            Section::make('Payment Details')
                ->description('Manage the payment method and transaction reference.')
                ->icon('heroicon-o-credit-card')
                ->columns(2)
                ->schema([
                    Select::make('payment_method')
                        ->options([
                            'cod' => 'Cash on Delivery',
                            'online' => 'Online Payment',
                        ])
                        ->live()
                        ->required(),

                    Select::make('payment_provider')
                        ->options([
                            'bkash' => 'bKash',
                            'nagad' => 'Nagad',
                            'rocket' => 'Rocket',
                            'sslcommerz' => 'SSLCOMMERZ',
                            'other' => 'Other',
                        ])
                        ->visible(
                            fn (Get $get): bool =>
                                $get('payment_method') === 'online'
                        )
                        ->required(
                            fn (Get $get): bool =>
                                $get('payment_method') === 'online'
                        )
                        ->nullable(),

                    TextInput::make('transaction_id')
                        ->label('Transaction ID')
                        ->maxLength(255)
                        ->visible(
                            fn (Get $get): bool =>
                                $get('payment_method') === 'online'
                        )
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make('Steadfast Courier')
                ->description('Add or update the courier tracking reference.')
                ->icon('heroicon-o-truck')
                ->schema([
                    TextInput::make('steadfast_tracking_number')
                        ->label('Steadfast Tracking Number')
                        ->placeholder('Enter tracking number')
                        ->maxLength(255)
                        ->nullable()
                        ->suffixAction(
                            \Filament\Actions\Action::make('copyTracking')
                                ->icon('heroicon-o-clipboard')
                                ->action(
                                    fn () => null
                                )
                                ->visible(fn () => false)
                        ),
                ]),

            Section::make('Customer & Delivery Details')
                ->description('Correct customer contact or delivery information when necessary.')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextInput::make('customer_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->maxLength(30),

                    TextInput::make('email')
                        ->email()
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('district')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('area')
                        ->nullable()
                        ->maxLength(255),

                    Textarea::make('delivery_address')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('customer_note')
                        ->nullable()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
