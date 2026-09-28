<x-layouts.admin title="QRIS">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">QR pembayaran manual</p>
            <h2 class="mt-2 text-xl font-bold text-slate-800">Upload gambar QRIS</h2>
            <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">Gambar ini akan digunakan sebagai QR pembayaran pada alur checkout. Admin tetap memeriksa pembayaran secara manual dari mutasi atau bukti transfer.</p>
            <form method="POST" action="{{ route('admin.qris.upload') }}" enctype="multipart/form-data" class="mt-7 space-y-4">
                @csrf
                <label class="block text-sm font-semibold text-slate-700">Pilih gambar QRIS
                    <input type="file" name="qris_image" accept="image/jpeg,image/png,image/webp" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:font-semibold file:text-teal-700">
                </label>
                <p class="text-xs text-slate-400">Format JPG, PNG, atau WebP. Ukuran maksimal 5 MB.</p>
                <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">Simpan QRIS</button>
            </form>
        </section>
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold text-slate-800">QRIS aktif</p>
            @if($qrisUrl)
                <img src="{{ $qrisUrl }}?v={{ filemtime(public_path(parse_url($qrisUrl, PHP_URL_PATH))) ?: time() }}" alt="QRIS pembayaran aktif" class="mx-auto mt-5 aspect-square w-56 max-w-full object-contain border border-slate-200 p-2">
                <p class="mt-4 text-center text-sm text-emerald-700">QRIS siap digunakan.</p>
            @else
                <div class="mt-5 border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">Belum ada gambar QRIS. Upload gambar untuk mengaktifkannya.</div>
            @endif
        </section>
    </div>
</x-layouts.admin>
