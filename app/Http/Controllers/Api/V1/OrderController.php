<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Order milik user yang sedang login. Customer di-resolve dari token,
 * bukan dari parameter request, agar user tidak bisa mengintip order orang lain.
 */
class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        if (! $customer) {
            return response()->json($this->paginator([], $request));
        }

        $orders = Order::with('items.product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request, CheckoutService $checkout): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $customer = $user->customer()->first() ?? Customer::create([
            'user_id' => $user->id,
            'wa_name' => $user->name,
        ]);

        $order = $checkout->checkout($customer, $validated['items'], [
            'shipping_address' => $validated['shipping_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($order, 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $customer = $this->customer($request);

        $order = Order::with('customer', 'items.product', 'payments')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('order_number', $id);
            })
            ->when($customer, fn ($q) => $q->where('customer_id', $customer->id))
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        return response()->json($order);
    }

    private function customer(Request $request): ?Customer
    {
        return $request->user()->customer()->first();
    }

    /** Paginator kosong dengan shape yang sama seperti paginate(). */
    private function paginator(array $data, Request $request): array
    {
        return [
            'current_page' => 1, 'data' => $data, 'total' => 0,
            'per_page' => 10, 'last_page' => 1,
        ];
    }
}
