<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * Katalog produk untuk mobile. Publik (tanpa login),
 * hanya menampilkan produk aktif. Sama sumber datanya dengan bot.
 */
class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when(request('category'), fn ($q, $category) => $q->where('category', $category))
            ->when(request('q'), fn ($q, $q2) => $q->where('name', 'like', "%{$q2}%"))
            ->latest()
            ->paginate(15);

        return response()->json($products);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::query()->where('is_active', true)->findOrFail($id);

        return response()->json($product);
    }
}
