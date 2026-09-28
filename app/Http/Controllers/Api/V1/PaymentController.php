<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Payment untuk mobile. Order & payment selalu di-scope ke user login.
 * Mendukung upload bukti transfer (multipart) atau URL bukti.
 */
class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user()->customer()->first();

        if (! $customer) {
            return response()->json(['current_page' => 1, 'data' => [], 'total' => 0, 'per_page' => 15, 'last_page' => 1]);
        }

        $payments = Payment::with('order')
            ->whereIn('order_id', Order::where('customer_id', $customer->id)->select('id'))
            ->latest()
            ->paginate(15);

        return response()->json($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:50',
            'proof_url' => 'nullable|string|max:500',
            'proof_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $customer = $request->user()->customer()->first();
        $order = Order::findOrFail($validated['order_id']);

        if (! $customer || $order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $proofUrl = $validated['proof_url'] ?? null;

        if ($request->hasFile('proof_image')) {
            $directory = public_path('uploads/payments');
            File::ensureDirectoryExists($directory);
            $filename = 'proof_'.$order->id.'_'.now()->format('YmdHis').'.'.$request->file('proof_image')->extension();
            $request->file('proof_image')->move($directory, $filename);
            $proofUrl = asset('uploads/payments/'.$filename);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'proof_url' => $proofUrl,
            'status' => 'pending',
        ]);

        return response()->json($payment->load('order'), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $customer = $request->user()->customer()->first();

        $payment = Payment::with('order')->find($id);

        if (! $payment || ! $customer || $payment->order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Payment tidak ditemukan'], 404);
        }

        return response()->json($payment);
    }
}
