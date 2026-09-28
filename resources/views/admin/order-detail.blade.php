<x-layouts.admin title="Detail Pesanan">
    @php
        $statusColors = [
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
            'shipped' => 'bg-violet-50 text-violet-700 border-violet-200',
            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
        ];
        $payColors = [
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
            'refunded' => 'bg-slate-100 text-slate-600 border-slate-200',
        ];
        $tanggal = $order->ordered_at ?? $order->created_at;
    @endphp

    <style>
        .receipt-paper {
            --zigzag: 10px;
            position: relative;
            filter: drop-shadow(0 10px 20px rgb(15 23 42 / 0.12));
        }
        .receipt-paper::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: calc(var(--zigzag) * -1);
            height: var(--zigzag);
            background:
                linear-gradient(-45deg, transparent 75%, white 0) 0 0 / 20px 20px repeat-x,
                linear-gradient(45deg, transparent 75%, white 0) 10px 0 / 20px 20px repeat-x;
            transform: rotate(180deg);
        }
        .receipt-dashed { border-top: 2px dashed #cbd5e1; }
        @media print {
            aside, header { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; }
            .print-hidden { display: none !important; }
            #struk-wrapper { box-shadow: none !important; margin: 0 auto !important; }
            .receipt-paper { filter: none; }
        }
    </style>

    {{-- Header aksi --}}
    <div class="print-hidden mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Kembali
            </a>
            <div>
                <p class="text-xs text-slate-500">Nomor pesanan</p>
                <h2 class="text-xl font-bold text-slate-800">{{ $order->order_number }}</h2>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">{{ $order->status }}</span>
            <span class="rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $payColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">{{ $order->payment_status }}</span>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-teal-700 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                Cetak Struk
            </button>
        </div>
    </div>

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_330px]">
        {{-- Kolom kiri: manajemen --}}
        <div class="print-hidden space-y-6">
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-5"><h3 class="font-semibold text-slate-800">Item pesanan</h3></div>
                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <div class="flex items-start justify-between gap-5 p-5">
                            <div>
                                <p class="font-medium text-slate-800">{{ $item->product?->name ?? 'Produk dihapus' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $item->quantity }} pcs x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between border-t border-slate-200 bg-slate-50 p-5">
                    <span class="font-semibold text-slate-700">Total</span>
                    <span class="text-lg font-bold text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </section>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Pelanggan</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $order->customer?->wa_name ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">WhatsApp</dt><dd class="font-medium break-all">{{ $order->customer?->wa_id ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Alamat</dt><dd class="font-medium">{{ $order->shipping_address ?: ($order->customer?->address ?? '-') }}</dd></div>
                        @if($order->notes)<div><dt class="text-slate-500">Catatan</dt><dd class="font-medium">{{ $order->notes }}</dd></div>@endif
                    </dl>
                </section>
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Pembayaran</h3>
                    @forelse($order->payments as $payment)
                        <div class="mt-3 border-t border-slate-100 pt-3 text-sm first:mt-4 first:border-0 first:pt-0">
                            <p class="font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }} - {{ ucfirst($payment->status) }}</p>
                            <p class="mt-1 text-slate-500">{{ $payment->method ?: 'Metode belum diisi' }}</p>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-slate-500">Belum ada pembayaran tercatat.</p>
                    @endforelse
                    @if($order->payment_proof_url)
                        <a href="{{ $order->payment_proof_url }}" target="_blank" class="mt-3 inline-flex text-xs font-semibold text-teal-700 hover:text-teal-900">Lihat bukti bayar</a>
                    @endif
                </section>
            </div>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-slate-800">Status pesanan</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                    @csrf @method('PUT')
                    <label class="block text-sm font-medium text-slate-700">Pesanan
                        <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 bg-white p-2 text-sm">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                                <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">Pembayaran
                        <select name="payment_status" class="mt-1 w-full rounded-lg border border-slate-300 bg-white p-2 text-sm">
                            @foreach(['pending','paid','failed','refunded'] as $status)
                                <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Simpan
                    </button>
                </form>
            </section>
        </div>

        {{-- Kolom kanan: STRUK --}}
        <div id="struk-wrapper" class="mx-auto w-full max-w-[330px]">
            <div class="receipt-paper rounded-t-xl bg-white px-6 pb-8 pt-6 font-mono text-[13px] leading-relaxed text-slate-900">
                {{-- Kop toko --}}
                <div class="text-center">
                    <p class="text-base font-black tracking-wide">{{ strtoupper($store['store_name'] ?? 'ORDERBOT STORE') }}</p>
                    <p class="mt-1 text-[11px] text-slate-600">{{ $store['store_address'] ?? '-' }}</p>
                    <p class="text-[11px] text-slate-600">WA: {{ $store['store_phone'] ?? '-' }}</p>
                </div>

                <div class="receipt-dashed mt-4 pt-3 text-[12px]">
                    <div class="flex justify-between gap-2"><span class="text-slate-500">No. Struk</span><span class="font-bold">{{ $order->order_number }}</span></div>
                    <div class="flex justify-between gap-2"><span class="text-slate-500">Tanggal</span><span>{{ $tanggal ? $tanggal->format('d/m/Y H:i') : '-' }}</span></div>
                    <div class="flex justify-between gap-2"><span class="text-slate-500">Kasir</span><span>{{ auth()->user()?->name ?? 'Admin' }}</span></div>
                    <div class="flex justify-between gap-2"><span class="text-slate-500">Pelanggan</span><span class="text-right font-medium">{{ $order->customer?->wa_name ?? '-' }}</span></div>
                </div>

                <div class="receipt-dashed mt-3 pt-3">
                    @foreach($order->items as $item)
                        <div class="mb-2.5">
                            <p class="font-semibold">{{ $item->product?->name ?? 'Produk dihapus' }}</p>
                            <div class="flex items-center justify-between gap-2 text-[12px]">
                                <span class="text-slate-600">{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                <span class="font-semibold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="receipt-dashed mt-3 space-y-1 pt-3 text-[12px]">
                    <div class="flex justify-between"><span class="text-slate-600">Subtotal ({{ $order->items->sum('quantity') }} item)</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
                    @if($order->payment_method)
                        <div class="flex justify-between"><span class="text-slate-600">Metode</span><span class="uppercase">{{ $order->payment_method }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-slate-600">Bayar</span><span class="font-semibold uppercase">{{ $order->payment_status }}</span></div>
                    <div class="mt-2 flex items-center justify-between border-t border-slate-200 pt-2 text-sm">
                        <span class="font-black">TOTAL</span>
                        <span class="text-base font-black">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="receipt-dashed mt-3 pt-3 text-center">
                    <p class="text-lg font-black tracking-[0.3em]">*{{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Z0-9]/', '', strtoupper($order->order_number)), 12, '') }}*</p>
                    <p class="mt-2 font-bold">*** TERIMA KASIH ***</p>
                    <p class="mt-1 text-[11px] text-slate-600">{{ $store['store_footer'] ?? 'Barang yang sudah dibeli tidak dapat ditukar / dikembalikan' }}</p>
                    <p class="mt-2 text-[10px] text-slate-400">Dicetak {{ now()->format('d/m/Y H:i') }} • {{ $store['store_name'] ?? 'OrderBot' }}</p>
                </div>
            </div>

            <button onclick="window.print()" class="print-hidden mt-6 w-full rounded-xl bg-slate-900 py-2.5 font-sans text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 active:scale-[0.98]">
                Cetak / Simpan PDF
            </button>
            <p class="print-hidden mt-2 text-center font-sans text-[11px] text-slate-400">Ukuran kertas 58mm / 80mm siap cetak</p>
        </div>
    </div>
</x-layouts.admin>
