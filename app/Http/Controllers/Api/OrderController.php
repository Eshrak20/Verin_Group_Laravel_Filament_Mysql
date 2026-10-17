<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'data' => [
                    'order_number' => $order->order_number,
                    'order_status' => $order->order_status,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'subtotal' => $order->subtotal,
                    'delivery_charge' => $order->delivery_charge,
                    'discount_amount' => $order->discount_amount,
                    'total_amount' => $order->total_amount,
                    'items' => $order->items->map(fn ($item) => [
                        'product_name' => $item->product_name,
                        'variant_name' => $item->variant_name,
                        'sku' => $item->sku,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'line_total' => $item->line_total,
                    ]),
                ],
            ], 201);
        } catch (Throwable $exception) {
            Log::error('Order creation failed.', [
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to place your order. Please try again.',
            ], 500);
        }
    }
}