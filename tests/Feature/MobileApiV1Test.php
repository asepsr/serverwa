<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function register(array $overrides = []): array
    {
        $res = $this->postJson('/api/v1/auth/register', array_merge([
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
        ], $overrides));

        $res->assertCreated();

        return $res->json();
    }

    private function authHeaders(string $token): array
    {
        return ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];
    }

    /**
     * Helper request yang selalu me-resolve ulang guard auth.
     * (Dalam satu test, aplikasi dipakai ulang antar-request sehingga
     * memoization user di guard harus dibersihkan tiap ganti kredensial.)
     */
    private function api(string $method, string $uri, array $data = [], ?string $token = null)
    {
        app('auth')->forgetGuards();

        $headers = $token ? $this->authHeaders($token) : ['Accept' => 'application/json'];

        return $this->json($method, $uri, $data, $headers);
    }

    public function test_register_login_me_logout_flow(): void
    {
        $reg = $this->register();
        $this->assertArrayHasKey('token', $reg);
        $this->assertEquals('Bearer', $reg['token_type']);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'mobile@example.com',
            'password' => 'password123',
            'device_name' => 'flutter-test',
        ]);
        $login->assertOk();
        $token = $login->json('token');

        $me = $this->api('GET', '/api/v1/me', [], $token);
        $me->assertOk()->assertJsonPath('user.email', 'mobile@example.com');

        $this->api('POST', '/api/v1/auth/logout', [], $token)->assertOk();

        // Token sudah di-revoke.
        $this->api('GET', '/api/v1/me', [], $token)->assertUnauthorized();
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->api('GET', '/api/v1/me')->assertUnauthorized();
        $this->api('GET', '/api/v1/orders')->assertUnauthorized();
        $this->api('POST', '/api/v1/orders', ['items' => []])->assertUnauthorized();
    }

    public function test_products_are_public(): void
    {
        Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10, 'is_active' => true]);

        $this->getJson('/api/v1/products')->assertOk()->assertJsonPath('total', 1);
    }

    public function test_order_and_payment_flow_scoped_to_user(): void
    {
        $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10, 'is_active' => true]);

        $token = $this->register()['token'];
        $h = $token; // dipakai via helper api()

        $order = $this->api('POST', '/api/v1/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'shipping_address' => 'Jl. Test No. 1',
        ], $h);
        $order->assertCreated()->assertJsonPath('total_price', '30000.00');

        $orderNumber = $order->json('order_number');
        $orderId = $order->json('id');

        $this->api('GET', '/api/v1/orders', [], $h)->assertOk()->assertJsonPath('total', 1);
        $this->api('GET', "/api/v1/orders/{$orderNumber}", [], $h)->assertOk();

        $payment = $this->api('POST', '/api/v1/payments', [
            'order_id' => $orderId,
            'amount' => 30000,
            'method' => 'transfer',
        ], $h);
        $payment->assertCreated()->assertJsonPath('status', 'pending');

        // User lain tidak bisa melihat / membayar order ini.
        $intruder = $this->register(['email' => 'intruder@example.com'])['token'];
        $this->api('GET', "/api/v1/orders/{$orderNumber}", [], $intruder)->assertNotFound();
        $this->api('POST', '/api/v1/payments', [
            'order_id' => $orderId, 'amount' => 30000, 'method' => 'transfer',
        ], $intruder)->assertNotFound();
    }

    public function test_bot_api_still_requires_static_key(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
    }
}
