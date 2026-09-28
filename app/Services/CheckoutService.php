<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

/**
 * Logika checkout bersama untuk semua pintu masuk (bot WA & mobile).
 *
 * Aturan: produk harus aktif, stok mencukupi, semua dalam 1 DB transaction,
 * nomor order di-generate server-side, total dihitung server-side.
 */
class CheckoutService
{
    /**
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function checkout(Customer $customer, array $items, array $extra = []): Order
    {
        $order = DB::transaction(function () use ($customer, $items, $extra) {
            $lines = collect($items)->map(function (array $item) {
                $product = Product::query()->where('is_active', true)->lockForUpdate()->find($item['product_id']);

                if (! $product || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ['Produk tidak tersedia atau stok tidak mencukupi.'],
                    ]);
                }

                return [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ];
            });

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'total_price' => $lines->sum('subtotal'),
                'shipping_address' => $extra['shipping_address'] ?? null,
                'notes' => $extra['notes'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['product']->price,
                    'subtotal' => $line['subtotal'],
                ]);

                $line['product']->decrement('stock', $line['quantity']);
            }

            $customer->increment('total_orders');

            return $order;
        });

        $order->load('customer', 'items.product');

        $dynamicQrisUrl = app(QrisConverter::class)->generate($order->order_number, $order->total_price);
        $order->setAttribute('qris_url', $dynamicQrisUrl ?: $this->staticQrisUrl());
        $order->setAttribute('qris_dynamic', $dynamicQrisUrl !== null);

        return $order;
    }

    public function staticQrisUrl(): ?string
    {
        $files = File::glob(public_path('uploads/qris/qris.*'));

        return $files ? asset('uploads/qris/'.basename($files[0])) : null;
    }
}
