<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            // Optional authentication: guests receive null.
            $userId = $request->user('sanctum')?->getAuthIdentifier();

            $order = $this->orderService->createOrder(
                $request->validated(),
                $request->user('sanctum')?->id,
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
                    'items' => $order->items->map(fn($item) => [
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'line_total' => $item->line_total,
                    ]),
                ],
            ], 201);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Order creation failed.', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to place your order. Please try again.',
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $perPage = min(
            max((int) $request->query('per_page', 10), 1),
            50
        );

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => [
                'orders' => $orders->getCollection()
                    ->map(fn(Order $order) => [
                        'order_number' => $order->order_number,
                        'order_status' => $order->order_status,
                        'payment_method' => $order->payment_method,
                        'payment_status' => $order->payment_status,
                        'subtotal' => $order->subtotal,
                        'delivery_charge' => $order->delivery_charge,
                        'discount_amount' => $order->discount_amount,
                        'total_amount' => $order->total_amount,
                        'items_count' => $order->items->sum('quantity'),
                        'created_at' => $order->created_at,
                    ])
                    ->values(),

                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'has_more_pages' => $orders->hasMorePages(),
                ],
            ],
        ]);
    }

    public function show(
        Request $request,
        string $orderNumber
    ): JsonResponse {
        $order = Order::query()
            ->where('user_id', $request->user('sanctum')->id)
            ->where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully.',
            'data' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'phone' => $order->phone,
                'email' => $order->email,
                'delivery_address' => $order->delivery_address,
                'district' => $order->district,
                'area' => $order->area,
                'delivery_zone' => $order->delivery_zone,
                'customer_note' => $order->customer_note,
                'order_status' => $order->order_status,
                'payment_method' => $order->payment_method,
                'payment_provider' => $order->payment_provider,
                'payment_status' => $order->payment_status,
                'subtotal' => $order->subtotal,
                'delivery_charge' => $order->delivery_charge,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,

                'items' => $order->items->map(fn($item) => [
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ])->values(),
            ],
        ]);
    }

    public function status(
        Request $request,
        string $orderNumber
    ): JsonResponse {
        $order = Order::query()
            ->where('user_id', $request->user('sanctum')->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Order status retrieved successfully.',
            'data' => [
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'updated_at' => $order->updated_at,
            ],
        ]);
    }
}
