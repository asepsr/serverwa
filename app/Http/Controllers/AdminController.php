<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\BotSetting;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard(): View
    {
        $days = 30;
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = Order::query()
            ->selectRaw("DATE(created_at) as d, COUNT(*) as c, SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as r")
            ->where('created_at', '>=', $from)
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $labels = $revenue = $orderCounts = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $revenue[] = (float) ($rows[$key]->r ?? 0);
            $orderCounts[] = (int) ($rows[$key]->c ?? 0);
        }

        return view('admin.dashboard', [
            'stats' => [
                'orders' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'revenue' => Order::where('payment_status', 'paid')->sum('total_price'),
                'customers' => Customer::count(),
            ],
            'orders' => Order::with('customer')->latest()->take(8)->get(),
            'chart' => ['labels' => $labels, 'revenue' => $revenue, 'orders' => $orderCounts],
        ]);
    }

    public function whatsapp(): View
    {
        return view('admin.whatsapp', ['botStatus' => $this->botStatus()]);
    }

    public function qris(): View
    {
        return view('admin.qris', ['qrisUrl' => $this->qrisUrl()]);
    }

    public function botMessages(): View
    {
        $defaults = $this->botMessageDefaults();

        $messages = array_merge($defaults, BotSetting::query()->pluck('value', 'key')->all());
        return view('admin.bot-messages', compact('messages'));
    }

    public function storeProfile(): View
    {
        return view('admin.store-profile', ['store' => $this->storeProfileData()]);
    }

    public function saveStoreProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:50',
            'store_footer' => 'nullable|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            BotSetting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        return back()->with('success', 'Profil toko berhasil disimpan. Struk akan memakai data baru.');
    }

    public function saveBotMessages(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_message' => 'required|string|max:5000',
            'help_message' => 'required|string|max:5000',
            'order_success_message' => 'required|string|max:5000',
            'payment_received_message' => 'required|string|max:5000',
            'order_shipped_message' => 'required|string|max:5000',
            'order_delivered_message' => 'required|string|max:5000',
            'order_cancelled_message' => 'required|string|max:5000',
            'order_status_message' => 'required|string|max:5000',
            'my_orders_message' => 'required|string|max:5000',
        ]);

        foreach ($data as $key => $value) {
            BotSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Pesan bot berhasil diperbarui.');
    }

    public function uploadQris(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $directory = public_path('uploads/qris');
        File::ensureDirectoryExists($directory);

        foreach (File::glob($directory.'/qris.*') as $oldFile) {
            File::delete($oldFile);
        }

        $extension = $validated['qris_image']->extension();
        $validated['qris_image']->move($directory, 'qris.'.$extension);

        return back()->with('success', 'Gambar QRIS berhasil diunggah.');
    }

    public function resetWhatsAppSession(): RedirectResponse
    {
        $response = $this->botRequest('post', '/session/reset');

        return back()->with(
            $response['success'] ? 'success' : 'error',
            $response['success'] ? 'Sesi WhatsApp direset. Muat ulang halaman untuk melihat QR baru.' : $response['message']
        );
    }

    public function logoutWhatsApp(): RedirectResponse
    {
        $response = $this->botRequest('post', '/logout');

        return back()->with(
            $response['success'] ? 'success' : 'error',
            $response['success'] ? 'Perangkat WhatsApp telah logout.' : $response['message']
        );
    }

    public function products(): View
    {
        return view('admin.products', [
            'products' => Product::latest()->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function saveProduct(Request $request, ?Product $product = null): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', 'category' => 'nullable|string|max:100',
            'description' => 'nullable|string', 'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        ($product ?? new Product())->fill($data)->save();
        return back()->with('success', 'Produk disimpan.');
    }

    public function deleteProduct(Product $product): RedirectResponse
    {
        $product->delete();
        return back()->with('success', 'Produk dihapus.');
    }

    public function categories(): View
    {
        return view('admin.categories', ['categories' => Category::withCount('products')->latest()->get()]);
    }

    public function saveCategory(Request $request, ?Category $category = null): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.($category?->id ?? 'NULL').',id',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        ($category ?? new Category())->fill($data)->save();
        return back()->with('success', 'Kategori disimpan.');
    }

    public function deleteCategory(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori dipakai produk, tidak bisa dihapus.');
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    public function orders(): View
    {
        return view('admin.orders', ['orders' => Order::with('customer', 'items.product')->latest()->get()]);
    }

    public function showOrder(Order $order): View
    {
        $order->load(['customer', 'items.product', 'payments.verifiedBy']);

        return view('admin.order-detail', ['order' => $order, 'store' => $this->storeProfileData()]);
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);
        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;
        $order->update($data);

        $statusChanged = $oldStatus !== $order->status;
        $paymentChanged = $oldPaymentStatus !== $order->payment_status;

        $templateKey = null;
        if ($statusChanged) {
            $templateKey = match ($order->status) {
                'shipped' => 'order_shipped_message',
                'delivered' => 'order_delivered_message',
                'cancelled' => 'order_cancelled_message',
                'processing' => $order->payment_status === 'paid' ? 'payment_received_message' : null,
                default => null,
            };
        } elseif ($paymentChanged && $order->payment_status === 'paid' && $order->status === 'processing') {
            $templateKey = 'payment_received_message';
        }

        if ($templateKey && $order->customer?->wa_id) {
            $order->load('customer', 'items.product');
            $defaults = $this->botMessageDefaults();
            $template = BotSetting::query()->where('key', $templateKey)->value('value')
                ?: ($defaults[$templateKey] ?? '');
            $message = strtr($template, $this->orderPlaceholders($order));

            Http::timeout(5)
                ->withHeaders(['X-Bot-API-Key' => config('services.whatsapp.api_key')])
                ->post(config('bot.service_url').'/send', [
                    'waId' => $order->customer->wa_id,
                    'message' => $message,
                ]);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    private function orderPlaceholders(Order $order): array
    {
        $items = $order->items->map(function ($item) {
            $productName = $item->product?->name ?? 'Produk tidak tersedia';
            return "• {$productName} x{$item->quantity} = Rp ".number_format($item->subtotal, 0, ',', '.');
        })->implode("\n");

        return [
            '{order_number}' => $order->order_number,
            '{items}' => $items,
            '{total_price}' => 'Rp '.number_format($order->total_price, 0, ',', '.'),
            '{payment_status}' => ucfirst($order->payment_status),
            '{status}' => ucfirst($order->status),
        ];
    }

    public function users(): View
    {
        return view('admin.users', ['users' => User::latest()->get()]);
    }

    public function saveUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8', 'role' => 'required|in:admin,user',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return back()->with('success', 'Pengguna baru dibuat.');
    }

    private function botStatus(): array
    {
        return $this->botRequest('get', '/status');
    }

    private function storeProfileData(): array
    {
        $defaults = [
            'store_name' => 'ORDERBOT STORE',
            'store_address' => 'Jl. Contoh No. 123, Kota Anda',
            'store_phone' => '08xx-xxxx-xxxx',
            'store_footer' => 'Barang yang sudah dibeli tidak dapat ditukar / dikembalikan',
        ];

        return array_merge($defaults, BotSetting::query()->whereIn('key', array_keys($defaults))->pluck('value', 'key')->all());
    }

    private function qrisUrl(): ?string
    {
        $files = File::glob(public_path('uploads/qris/qris.*'));
        return $files ? asset('uploads/qris/'.basename($files[0])) : null;
    }

    private function botMessageDefaults(): array
    {
        return [
            'menu_message' => '*MENU UTAMA*\n\n1. *PRODUCT* - Lihat daftar produk dan kode\n2. *ORDER* - Mulai pilih produk untuk keranjang\n3. *ORDER_STATUS <nomor>* - Cek status\n4. *MY_ORDERS* - Pesanan saya\n5. *HELP* - Bantuan',
            'help_message' => '*BANTUAN ORDER*\n\n1. Ketik *PRODUCT* untuk melihat kode produk.\n2. Ketik *ORDER*.\n3. Kirim kode produk.\n4. Kirim jumlah pcs.\n5. Ketik *LANJUT* untuk tambah produk atau *CHECKOUT* untuk bayar.\n\nKetik *CANCEL* untuk membatalkan keranjang.',
            'order_success_message' => '✅ *Pesanan Berhasil!*\n\n🔢 Nomor Order: *{order_number}*\n💰 Total: *{total_price}*\n📦 Status: *Menunggu pembayaran*\n\nKirim bukti transfer ke chat ini untuk melanjutkan.',
            'payment_received_message' => "✅ *PEMBAYARAN DITERIMA*\n\n🧾 *INVOICE*\nNomor: *{order_number}*\n\n🛍️ *DETAIL PESANAN*\n{items}\n\n💰 Total: *{total_price}*\n💳 Status pembayaran: *{payment_status}*\n📦 Status pesanan: *{status}*\n\nPesanan Anda sedang kami proses. Terima kasih.",
            'order_shipped_message' => "🚚 *PESANAN SEDANG DIKIRIM*\n\nHalo, pesanan Anda sedang dikirim!\n\n🔢 Nomor Order: *{order_number}*\n\n🛍️ *DETAIL PESANAN*\n{items}\n\n💰 Total: *{total_price}*\n📦 Status pesanan: *{status}*\n\nMohon siapkan penerimaan. Terima kasih.",
            'order_delivered_message' => "✅ *PESANAN SUDAH DITERIMA*\n\n🔢 Nomor Order: *{order_number}*\n\n🛍️ *DETAIL PESANAN*\n{items}\n\n💰 Total: *{total_price}*\n\nPesanan Anda sudah diterima. Terima kasih sudah berbelanja!",
            'order_cancelled_message' => "❌ *PESANAN DIBATALKAN*\n\n🔢 Nomor Order: *{order_number}*\n💰 Total: *{total_price}*\n\nPesanan Anda dibatalkan. Hubungi admin untuk informasi lebih lanjut.",
            'order_status_message' => "📋 *STATUS PESANAN*\n\n🔢 Nomor: *{order_number}*\n\n🛍️ *DETAIL PESANAN*\n{items}\n\n💰 Total: *{total_price}*\n💳 Pembayaran: *{payment_status}*\n📦 Status: *{status}*\n\n{status_note}",
            'my_orders_message' => "📦 *PESANAN SAYA*\n\n{orders}\n\nKetik *ORDER_STATUS <nomor>* untuk melihat detail.",
        ];
    }

    private function botRequest(string $method, string $path): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-Bot-API-Key' => config('services.whatsapp.api_key')])
                ->{$method}(config('bot.service_url').$path);

            return $response->successful()
                ? $response->json()
                : ['success' => false, 'message' => 'Layanan bot tidak dapat dihubungi.'];
        } catch (\Throwable) {
            return ['success' => false, 'message' => 'Layanan bot tidak aktif.'];
        }
    }
}
