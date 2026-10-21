<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Models\ProductVariant;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Steadfast Courier')
                ->schema([
                    TextInput::make('steadfast_tracking_number')
                        ->label('Steadfast Tracking Number')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
            Section::make('Order Status')
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
                        ->default('pending')
                        ->required()
                        ->disabled(fn($livewire) => $livewire instanceof CreateOrder),

                    Select::make('payment_status')
                        ->options([
                            'unpaid' => 'Unpaid',
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('unpaid')
                        ->required()
                        ->disabled(fn($livewire) => $livewire instanceof CreateOrder),
                ]),

            Section::make('Customer Information')
                ->columns(2)
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

                    Select::make('payment_method')
                        ->options([
                            'cod' => 'Cash on Delivery',
                            'online' => 'Online Payment',
                        ])
                        ->default('cod')
                        ->live()
                        ->required(),

                    Textarea::make('delivery_address')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('district')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('area')
                        ->nullable()
                        ->maxLength(255),

                    Textarea::make('customer_note')
                        ->nullable()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Payment Details')
                ->columns(2)
                ->schema([
                    Select::make('payment_provider')
                        ->options([
                            'bkash' => 'bKash',
                            'nagad' => 'Nagad',
                            'rocket' => 'Rocket',
                            'sslcommerz' => 'SSLCOMMERZ',
                            'other' => 'Other',
                        ])
                        ->visible(fn(Get $get) => $get('payment_method') === 'online')
                        ->required(fn(Get $get) => $get('payment_method') === 'online')
                        ->nullable(),

                    TextInput::make('transaction_id')
                        ->label('Transaction ID')
                        ->maxLength(255)
                        ->visible(fn(Get $get) => $get('payment_method') === 'online')
                        ->nullable(),
                ]),

            Section::make('Delivery')
                ->description('Delivery charges are calculated from each product configuration.')
                ->schema([
                    Select::make('delivery_zone')
                        ->options([
                            'inside_dhaka' => 'Inside Dhaka',
                            'outside_dhaka' => 'Outside Dhaka',
                        ])
                        ->live()
                        ->required(),
                ]),

            Section::make('Order Items')
                ->description('Choose product variants and quantities. Prices are loaded from the database.')
                ->schema([
                    Repeater::make('items')
                        ->label('Products')
                        ->schema([
                            Select::make('product_variant_id')
                                ->label('Product / Variant')
                                ->options(fn(): array => ProductVariant::query()
                                    ->with('product')
                                    ->get()
                                    ->filter(fn($variant) => $variant->product !== null)
                                    ->mapWithKeys(fn($variant) => [
                                        $variant->id => sprintf(
                                            '%s — SKU: %s — ৳%s',
                                            $variant->product->name,
                                            $variant->sku ?? 'N/A',
                                            number_format(
                                                (float) (
                                                    $variant->sale_price > 0
                                                    && $variant->sale_price < $variant->price
                                                    ? $variant->sale_price
                                                    : $variant->price
                                                ),
                                                2
                                            )
                                        ),
                                    ])
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required(),

                            TextInput::make('quantity')
                                ->numeric()
                                ->integer()
                                ->default(1)
                                ->minValue(1)
                                ->required()
                                ->live(),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->addActionLabel('Add Another Product')
                        ->reorderable(false)
                        ->live()
                        ->columnSpanFull(),
                ]),

            Section::make('Order Summary')
                ->description('Estimated totals update when products, quantities, or delivery zone change. Final amounts are recalculated on the server.')
                ->schema([
                    Placeholder::make('calculated_totals')
                        ->label('Estimated Price Breakdown')
                        ->content(function (Get $get): string {
                            $items = $get('items') ?? [];
                            $zone = $get('delivery_zone');

                            if (empty($items)) {
                                return 'Add at least one product to calculate the order total.';
                            }

                            $subtotal = 0;
                            $discount = 0;
                            $delivery = 0;
                            $chargedProducts = [];

                            $chargeColumn = match ($zone) {
                                'inside_dhaka' => 'inside_dhaka_charge',
                                'outside_dhaka' => 'outside_dhaka_charge',
                                default => null,
                            };

                            foreach ($items as $item) {
                                $variantId = $item['product_variant_id'] ?? null;
                                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                                if (! $variantId) {
                                    continue;
                                }

                                $variant = ProductVariant::query()
                                    ->with('product.deliveryCharge')
                                    ->find($variantId);

                                if (! $variant || ! $variant->product) {
                                    continue;
                                }

                                $regular = (float) $variant->price;
                                $sale = $variant->sale_price !== null
                                    ? (float) $variant->sale_price
                                    : null;

                                $hasDiscount = $sale !== null
                                    && $sale > 0
                                    && $sale < $regular;

                                $unitPrice = $hasDiscount ? $sale : $regular;

                                $subtotal += $unitPrice * $quantity;

                                if ($hasDiscount) {
                                    $discount += ($regular - $unitPrice) * $quantity;
                                }

                                $productId = $variant->product->id;

                                if (
                                    $chargeColumn
                                    && ! isset($chargedProducts[$productId])
                                ) {
                                    $charge = $variant->product->deliveryCharge;

                                    if ($charge && is_numeric($charge->{$chargeColumn})) {
                                        $delivery += (float) $charge->{$chargeColumn};
                                    }

                                    $chargedProducts[$productId] = true;
                                }
                            }

                            $total = $subtotal + $delivery;

                            return implode("\n", [
                                'Subtotal: ৳' . number_format($subtotal, 2),
                                'Discount included in product prices: ৳' . number_format($discount, 2),
                                'Delivery charge: ৳' . number_format($delivery, 2),
                                'Total payable: ৳' . number_format($total, 2),
                            ]);
                        }),
                ]),


        ]);
    }
}
