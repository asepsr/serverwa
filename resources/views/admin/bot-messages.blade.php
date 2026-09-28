<x-layouts.admin title="Pesan Bot">
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6"><h2 class="text-xl font-bold text-slate-800">Pengaturan jawaban bot</h2><p class="mt-1 text-sm text-slate-500">Ubah pesan tanpa menyentuh source code bot. Pesan status pesanan terkirim otomatis ke WhatsApp customer saat admin mengubah status. Gunakan placeholder yang tersedia.</p></div>
        <form method="POST" action="{{ route('admin.bot-messages.update') }}" class="space-y-8">@csrf @method('PUT')
            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500">Pesan umum</h3>
                <div class="space-y-6">
                    @foreach(['menu_message' => 'Pesan MENU', 'help_message' => 'Pesan HELP', 'order_success_message' => 'Pesan invoice/order berhasil', 'payment_received_message' => 'Pesan pembayaran diterima (processing + paid)'] as $key => $label)
                        <label class="block text-sm font-semibold text-slate-700">{{ $label }}<textarea name="{{ $key }}" rows="7" class="mt-2 w-full rounded-lg border border-slate-300 p-3 font-mono text-sm leading-6 outline-none focus:border-teal-500">{{ old($key, $messages[$key]) }}</textarea></label>
                    @endforeach
                </div>
            </div>
            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500">Pesan status pesanan (otomatis saat admin ubah status)</h3>
                <div class="space-y-6">
                    @foreach(['order_shipped_message' => 'Pesan status SHIPPED — pesanan sedang dikirim', 'order_delivered_message' => 'Pesan status DELIVERED — pesanan sudah diterima customer', 'order_cancelled_message' => 'Pesan status CANCELLED — pesanan dibatalkan'] as $key => $label)
                        <label class="block text-sm font-semibold text-slate-700">{{ $label }}<textarea name="{{ $key }}" rows="7" class="mt-2 w-full rounded-lg border border-slate-300 p-3 font-mono text-sm leading-6 outline-none focus:border-teal-500">{{ old($key, $messages[$key]) }}</textarea></label>
                    @endforeach
                </div>
            </div>
            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500">Pesan jawaban customer</h3>
                <div class="space-y-6">
                    @foreach(['order_status_message' => 'Balasan ORDER_STATUS <nomor> — status + detail pesanan', 'my_orders_message' => 'Balasan MY_ORDERS — daftar detail pesanan customer'] as $key => $label)
                        <label class="block text-sm font-semibold text-slate-700">{{ $label }}<textarea name="{{ $key }}" rows="7" class="mt-2 w-full rounded-lg border border-slate-300 p-3 font-mono text-sm leading-6 outline-none focus:border-teal-500">{{ old($key, $messages[$key]) }}</textarea></label>
                    @endforeach
                </div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-xs leading-6 text-slate-600">
                <p class="font-semibold text-slate-700">Keterangan placeholder</p>
                <ul class="mt-2 space-y-1">
                    <li><code>{order_number}</code> - nomor invoice/order, contoh: <strong>ORD-20260918-001</strong></li>
                    <li><code>{total_price}</code> - total pembayaran yang sudah diformat, contoh: <strong>Rp 250.000</strong></li>
                    <li><code>{items}</code> - daftar produk, jumlah, dan subtotal pesanan</li>
                    <li><code>{payment_status}</code> - status pembayaran, contoh: <strong>Paid</strong></li>
                    <li><code>{status}</code> - status pesanan, contoh: <strong>Processing</strong></li>
                    <li><code>{status_note}</code> - kalimat penjelasan status (khusus balasan ORDER_STATUS)</li>
                    <li><code>{orders}</code> - daftar detail pesanan customer (khusus balasan MY_ORDERS)</li>
                </ul>
                <p class="mt-3 font-medium text-amber-700">Ketik placeholder persis seperti contoh, termasuk tanda kurung kurawal <code>{ }</code>. Jangan mengubah nama di dalamnya.</p>
            </div>
            <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">Simpan pesan</button>
        </form>
    </section>
</x-layouts.admin>
