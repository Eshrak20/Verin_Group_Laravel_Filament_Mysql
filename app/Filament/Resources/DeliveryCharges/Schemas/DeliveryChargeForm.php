<?php

namespace App\Filament\Resources\DeliveryCharges\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeliveryChargeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery Charge Information')
                    ->description('Configure the delivery charges and availability for your products.')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        TextInput::make('name')
                            ->label('Delivery Charge Name')
                            ->placeholder('e.g. Standard Delivery')
                            ->helperText('Enter a name to identify this delivery charge.')
                            ->required()
                            ->maxLength(255),

                        Toggle::make('status')
                            ->label('Active Status')
                            ->helperText('Enable this delivery charge to make it available.')
                            ->default(true)
                            ->required(),

                        TextInput::make('inside_dhaka_charge')
                            ->label('Inside Dhaka')
                            ->prefix('৳')
                            ->placeholder('150.00')
                            ->helperText('Delivery charge for customers inside Dhaka.')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01'),

                        TextInput::make('outside_dhaka_charge')
                            ->label('Outside Dhaka')
                            ->prefix('৳')
                            ->placeholder('180.00')
                            ->helperText('Delivery charge for customers outside Dhaka.')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01'),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Add any additional delivery information...')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}