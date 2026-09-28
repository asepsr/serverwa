<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Auth khusus aplikasi mobile (token-based via Sanctum).
 * Tidak memakai X-Bot-API-Key; tiap user mendapat personal access token.
 */
class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'wa_name' => $user->name,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'customer' => $customer,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
        ]);

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->load('customer');

        $token = $user->createToken($validated['device_name'] ?? 'mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'customer' => $user->customer,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke token Bearer yang dibawa request ini secara eksplisit.
        // (Guard Sanctum mendahulukan sesi web bila ada, jadi token harus
        // dicari dari header, bukan hanya dari currentAccessToken().)
        $presented = $request->bearerToken();
        if ($presented) {
            \Laravel\Sanctum\PersonalAccessToken::findToken($presented)?->delete();
        }
        Auth::guard('web')->logout();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('customer');

        return response()->json([
            'user' => $user,
            'customer' => $user->customer,
        ]);
    }
}
