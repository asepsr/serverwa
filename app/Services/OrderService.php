<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = Order::create($data);
        return $order;
    }

    public function addItem(Order $order, array $itemData): OrderItem
    {
        $product = Product::findOrFail($itemData['product_id']);
        $subtotal = $product->price * $itemData['quantity'];

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $itemData['quantity'],
            'unit_price' => $product->price,
            'subtotal' => $subtotal,
        ]);
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order;
    }
}
