<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $payments = Payment::with('order')->paginate(15);
        return response()->json($payments);
    }

    public function show(string $id): JsonResponse
    {
        $payment = Payment::with('order')->findOrFail($id);
        return response()->json($payment);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric',
            'method' => 'required|string|max:50',
            'proof_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:20',
        ]);

        $payment = Payment::create($validated);
        return response()->json($payment, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->all());
        return response()->json($payment);
    }

    public function destroy(string $id): JsonResponse
    {
        Payment::findOrFail($id)->delete();
        return response()->json(['message' => 'Payment deleted']);
    }
}
