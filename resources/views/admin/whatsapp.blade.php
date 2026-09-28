<x-layouts.admin title="WhatsApp">
    @php
        $connection = $botStatus['connection'] ?? 'unavailable';
        $isConnected = $botStatus['success'] && $connection === 'connected';
        $isWaitingForQr = $botStatus['success'] && $connection === 'qr';
    @endphp
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <section class="border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Status perangkat</p>
            <div class="mt-3 flex items-center gap-3">
                <span class="h-3 w-3 rounded-full {{ $isConnected ? 'bg-emerald-500' : ($isWaitingForQr ? 'bg-amber-400' : 'bg-stone-400') }}"></span>
                <p class="text-2xl font-semibold capitalize">{{ $isConnected ? 'Terhubung' : ($isWaitingForQr ? 'Menunggu scan QR' : ($connection === 'connecting' ? 'Menghubungkan' : 'Tidak terhubung')) }}</p>
            </div>
            <dl class="mt-7 grid gap-5 border-t border-stone-100 pt-5 sm:grid-cols-2">
                <div><dt class="text-xs font-medium uppercase text-stone-500">Nomor bot</dt><dd class="mt-1 font-medium">{{ $botStatus['number'] ?? '-' }}</dd></div>
                <div><dt class="text-xs font-medium uppercase text-stone-500">Layanan bot</dt><dd class="mt-1 font-medium">{{ $botStatus['success'] ? 'Aktif' : 'Tidak tersedia' }}</dd></div>
            </dl>
            <div class="mt-8 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.whatsapp.reset') }}">@csrf<button class="bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Buat QR Baru</button></form>
                @if($isConnected)<form method="POST" action="{{ route('admin.whatsapp.logout') }}">@csrf<button class="border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">Logout Perangkat</button></form>@endif
            </div>
        </section>
        <section class="border border-stone-200 bg-white p-6">
            <p class="text-sm font-semibold">Pairing perangkat</p>
            @if($isWaitingForQr && !empty($botStatus['qr']))
                <img src="{{ $botStatus['qr'] }}" alt="WhatsApp pairing QR code" class="mx-auto mt-5 aspect-square w-48 max-w-full border border-stone-200 p-2">
                <p class="mt-4 text-center text-sm text-stone-600">Buka WhatsApp, pilih Perangkat tertaut, lalu scan kode ini.</p>
            @elseif($isConnected)
                <div class="mt-5 border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-800">Perangkat sudah terhubung. Gunakan “Buat QR Baru” bila ingin mengganti perangkat.</div>
            @else
                <div class="mt-5 border border-stone-200 bg-stone-50 p-5 text-sm text-stone-600">Klik “Buat QR Baru”, lalu muat ulang halaman ini beberapa saat kemudian.</div>
            @endif
        </section>
    </div>
</x-layouts.admin>
