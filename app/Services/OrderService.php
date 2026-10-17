<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                // Lock the variant row during order creation.
                $variant = ProductVariant::query()
                    ->with('product')
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
                $unitPrice = (float) $variant->price;

                if ($unitPrice < 0) {
                    throw ValidationException::withMessages([
                        'items' => ['A product variant has an invalid price.'],
                    ]);
                }

                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal = round($subtotal + $lineTotal, 2);

                $items[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            // Replace these with your actual delivery/discount rules.
            $deliveryCharge = 0;
            $discountAmount = 0;

            $totalAmount = round(
                $subtotal + $deliveryCharge - $discountAmount,
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
                'customer_note' => $data['customer_note'] ?? null,

                'payment_method' => $data['payment_method'],
                'payment_provider' => $isOnline
                    ? $data['payment_provider']
                    : null,
                'transaction_id' => $isOnline
                    ? $data['transaction_id']
                    : null,

                'payment_status' => $isOnline ? 'pending' : 'unpaid',
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

                    // Change this if your variant has another display field.
                    'variant_name' => $variant->name ?? null,

                    'sku' => $variant->sku ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order->load('items');
        }, 3);
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'VRN-' . strtoupper(Str::random(10));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}