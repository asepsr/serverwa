<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\QrisConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('customer', 'items.product')->paginate(15);
        return response()->json($orders);
    }

    public function show(string $id): JsonResponse
    {
        $order = Order::with('customer', 'items.product', 'payments')
            ->where('id', $id)
            ->orWhere('order_number', $id)
            ->firstOrFail();
        return response()->json($order);
    }

    public function myOrders(string $waId): JsonResponse
    {
        $customer = Customer::query()->where('wa_id', $waId)->first();

        if (! $customer) {
            return response()->json([]);
        }

        $orders = Order::with('items.product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->filled('wa_id')) {
            $validated = $request->validate([
                'wa_id' => 'required|string|max:50',
                'wa_name' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $order = DB::transaction(function () use ($validated) {
                $customer = Customer::updateOrCreate(
                    ['wa_id' => $validated['wa_id']],
                    ['wa_name' => $validated['wa_name'] ?? null]
                );

                $items = collect($validated['items'])->map(function (array $item) {
                    $product = Product::query()->where('is_active', true)->lockForUpdate()->find($item['product_id']);

                    if (! $product || $product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => ['Produk tidak tersedia atau stok tidak mencukupi.'],
                        ]);
                    }

                    return ['product' => $product, 'quantity' => $item['quantity'], 'subtotal' => $product->price * $item['quantity']];
                });

                $total = $items->sum('subtotal');
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'order_number' => 'ORD-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                    'total_price' => $total,
                    'ordered_at' => now(),
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->price,
                        'subtotal' => $item['subtotal'],
                    ]);

                    $item['product']->decrement('stock', $item['quantity']);
                }
                $customer->increment('total_orders');

                return $order;
            });

            $order->load('items.product');
            $dynamicQrisUrl = app(QrisConverter::class)->generate($order->order_number, $order->total_price);
            $order->setAttribute('qris_url', $dynamicQrisUrl ?: $this->qrisUrl());
            $order->setAttribute('qris_dynamic', $dynamicQrisUrl !== null);

            return response()->json($order, 201);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_number' => 'required|string|unique:orders,order_number',
            'total_price' => 'required|numeric',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    private function qrisUrl(): ?string
    {
        $files = File::glob(public_path('uploads/qris/qris.*'));

        return $files ? asset('uploads/qris/'.basename($files[0])) : null;
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return response()->json($order);
    }

    public function destroy(string $id): JsonResponse
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}
