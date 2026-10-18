<?php

namespace App\Services;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $discountAmount = 0;
            $deliveryCharge = 0;

            $items = [];
            $chargedProductIds = [];

            $deliveryZone = $data['delivery_zone'];

            $chargeColumn = match ($deliveryZone) {
                'inside_dhaka' => 'inside_dhaka_charge',
                'outside_dhaka' => 'outside_dhaka_charge',
                default => throw ValidationException::withMessages([
                    'delivery_zone' => [
                        'Please select inside or outside Dhaka.',
                    ],
                ]),
            };

            foreach ($data['items'] as $item) {
                // Lock the selected variant while processing the order.
                $variant = ProductVariant::query()
                    ->with('product.deliveryCharge')
                    ->lockForUpdate()
                    ->find($item['product_variant_id']);

                if (! $variant || ! $variant->product) {
                    throw ValidationException::withMessages([
                        'items' => [
                            'One or more selected product variants are unavailable.',
                        ],
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items' => [
                            'Quantity must be at least 1.',
                        ],
                    ]);
                }

                $product = $variant->product;

                // Calculate regular and discounted prices.
                $regularPrice = (float) $variant->price;

                $salePrice = $variant->sale_price !== null
                    ? (float) $variant->sale_price
                    : null;

                if ($regularPrice < 0) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Invalid regular price for product: {$product->name}.",
                        ],
                    ]);
                }

                $hasDiscount = $salePrice !== null
                    && $salePrice > 0
                    && $salePrice < $regularPrice;

                $unitPrice = $hasDiscount
                    ? $salePrice
                    : $regularPrice;

                $lineTotal = round($unitPrice * $quantity, 2);

                $lineDiscount = $hasDiscount
                    ? round(
                        ($regularPrice - $unitPrice) * $quantity,
                        2
                    )
                    : 0;

                $subtotal = round($subtotal + $lineTotal, 2);

                $discountAmount = round(
                    $discountAmount + $lineDiscount,
                    2
                );

                // Charge delivery once per distinct product,
                // regardless of quantity or variant count.
                $itemDeliveryCharge = 0;

                if (! isset($chargedProductIds[$product->id])) {
                    $charge = $product->deliveryCharge;

                    if (! $charge) {
                        throw ValidationException::withMessages([
                            'items' => [
                                "No delivery charge is configured for product: {$product->name}.",
                            ],
                        ]);
                    }

                    $productDeliveryCharge = $charge->{$chargeColumn};

                    if (
                        $productDeliveryCharge === null
                        || ! is_numeric($productDeliveryCharge)
                        || (float) $productDeliveryCharge < 0
                    ) {
                        throw ValidationException::withMessages([
                            'items' => [
                                "The selected delivery charge is invalid for product: {$product->name}.",
                            ],
                        ]);
                    }

                    $itemDeliveryCharge = round(
                        (float) $productDeliveryCharge,
                        2
                    );

                    $deliveryCharge = round(
                        $deliveryCharge + $itemDeliveryCharge,
                        2
                    );

                    $chargedProductIds[$product->id] = true;
                }

                $items[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'regular_unit_price' => $regularPrice,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $lineDiscount,
                    'line_total' => $lineTotal,
                    'delivery_charge' => $itemDeliveryCharge,
                ];
            }

            // Subtotal uses discounted prices.
            // Delivery is added once per distinct product.
            $totalAmount = round(
                $subtotal + $deliveryCharge,
                2
            );

            $isOnline = $data['payment_method'] === 'online';

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),

                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,

                'delivery_address' => $data['delivery_address'],
                'district' => $data['district'] ?? null,
                'area' => $data['area'] ?? null,
                'delivery_zone' => $deliveryZone,
                'customer_note' => $data['customer_note'] ?? null,

                'payment_method' => $data['payment_method'],

                'payment_provider' => $isOnline
                    ? ($data['payment_provider'] ?? null)
                    : null,

                'transaction_id' => $isOnline
                    ? ($data['transaction_id'] ?? null)
                    : null,

                'payment_status' => $isOnline
                    ? 'pending'
                    : 'unpaid',

                'order_status' => 'pending',

                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            foreach ($items as $item) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,

                    'unit_price' => $item['unit_price'],
                    'regular_unit_price' => $item['regular_unit_price'],
                    'discount_amount' => $item['discount_amount'],

                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],

                    // Saved per-item delivery breakdown.
                    'delivery_charge' => $item['delivery_charge'],
                ]);
            }

            return $order->load('items');
        }, 3);

        // Send the notification after the transaction commits.
        Mail::to(config('mail.order_notification_email'))
            ->send(new NewOrderNotification($order));

        return $order;
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'VRN-' . strtoupper(Str::random(10));
        } while (
            Order::where('order_number', $orderNumber)->exists()
        );

        return $orderNumber;
    }
}
