<x-layouts.admin title="Dashboard">
    <div class="mb-7 flex flex-wrap items-end justify-between gap-3"><div><p class="text-sm text-slate-500">Pantau transaksi dan aktivitas toko Anda.</p></div><a href="{{ route('admin.orders') }}" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Lihat pesanan</a></div>
    @php $cards = [['Total pesanan', $stats['orders'], 'bg-slate-900'], ['Menunggu proses', $stats['pending'], 'bg-amber-500'], ['Pendapatan diterima', 'Rp '.number_format($stats['revenue'], 0, ',', '.'), 'bg-teal-600'], ['Pelanggan', $stats['customers'], 'bg-rose-500']]; @endphp
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">@foreach ($cards as [$label, $value, $color])<section class="relative overflow-hidden rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><span class="absolute right-0 top-0 h-1.5 w-20 {{ $color }}"></span><p class="text-sm font-medium text-slate-500">{{ $label }}</p><p class="mt-3 text-2xl font-bold text-slate-800">{{ $value }}</p><p class="mt-4 text-xs text-slate-400">Data operasional saat ini</p></section>@endforeach</div>

    <section class="mt-8 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="font-bold text-slate-800">Grafik penjualan</h2><p class="mt-1 text-sm text-slate-500">Pendapatan (paid) & jumlah pesanan per hari</p></div>
            <div class="flex gap-2" id="salesRangeButtons">
                <button data-range="7" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition bg-slate-900 text-white">7 hari</button>
                <button data-range="14" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition bg-slate-100 text-slate-600 hover:bg-slate-200">14 hari</button>
                <button data-range="30" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition bg-slate-100 text-slate-600 hover:bg-slate-200">30 hari</button>
            </div>
        </div>
        <div class="relative mt-4 h-72"><canvas id="salesChart"></canvas></div>
    </section>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-sm"><div class="flex items-center justify-between border-b border-slate-100 px-4 py-4"><div><h2 class="font-bold text-slate-800">Pesanan terbaru</h2><p class="mt-1 text-sm text-slate-500">Transaksi terakhir dari WhatsApp</p></div><a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">Semua pesanan</a></div><div class="overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm" id="dashboardTable"><thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Nomor</th><th class="px-4 py-3">Pelanggan</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th></tr></thead><tbody>@forelse($orders as $order)<tr class="border-b border-slate-100 hover:bg-teal-50/40"><td class="px-4 py-4 font-semibold text-slate-700">{{ $order->order_number }}</td><td class="px-4 py-4 text-slate-600">{{ $order->customer?->wa_name ?? '-' }}</td><td class="whitespace-nowrap px-4 py-4 font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td><td class="px-4 py-4"><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold capitalize text-amber-700">{{ $order->status }}</span></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">Belum ada pesanan.</td></tr>@endforelse</tbody></table></div></section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const labels = @json($chart['labels']);
            const revenue = @json($chart['revenue']);
            const orders = @json($chart['orders']);
            const ctx = document.getElementById('salesChart');
            if (!ctx || typeof Chart === 'undefined') return;

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.slice(-7),
                    datasets: [
                        {
                            type: 'line',
                            label: 'Pendapatan (Rp)',
                            data: revenue.slice(-7),
                            borderColor: '#0d9488',
                            backgroundColor: 'rgba(13,148,136,0.12)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Pesanan',
                            data: orders.slice(-7),
                            backgroundColor: 'rgba(15,23,42,0.85)',
                            borderRadius: 5,
                            yAxisID: 'y1',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function (item) {
                                    if (item.dataset.label === 'Pendapatan (Rp)') {
                                        return ' Pendapatan: Rp ' + Number(item.raw).toLocaleString('id-ID');
                                    }
                                    return ' Pesanan: ' + item.raw;
                                },
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: function (v) { return v >= 1000000 ? (v / 1000000) + ' jt' : (v >= 1000 ? (v / 1000) + ' rb' : v); } } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } },
                    },
                },
            });

            document.querySelectorAll('#salesRangeButtons button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const n = parseInt(btn.dataset.range, 10);
                    chart.data.labels = labels.slice(-n);
                    chart.data.datasets[0].data = revenue.slice(-n);
                    chart.data.datasets[1].data = orders.slice(-n);
                    chart.update();
                    document.querySelectorAll('#salesRangeButtons button').forEach(function (b) {
                        b.className = 'rounded-lg px-3 py-1.5 text-xs font-semibold transition bg-slate-100 text-slate-600 hover:bg-slate-200';
                    });
                    btn.className = 'rounded-lg px-3 py-1.5 text-xs font-semibold transition bg-slate-900 text-white';
                });
            });
        })();
    </script>
</x-layouts.admin>
