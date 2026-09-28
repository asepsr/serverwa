<x-layouts.admin title="Profil Toko">
    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_330px]">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800">Pengaturan profil toko</h2>
                <p class="mt-1 text-sm text-slate-500">Nama, alamat, dan nomor HP ini otomatis tampil di kop struk pada halaman detail pesanan.</p>
            </div>
            <form method="POST" action="{{ route('admin.store-profile.update') }}" class="space-y-5">
                @csrf @method('PUT')
                <label class="block text-sm font-semibold text-slate-700">Nama toko
                    <input name="store_name" value="{{ old('store_name', $store['store_name']) }}" required maxlength="100" placeholder="Contoh: TOKO BERKAH JAYA" class="mt-2 w-full rounded-lg border border-slate-300 p-3 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </label>
                <label class="block text-sm font-semibold text-slate-700">Alamat toko
                    <textarea name="store_address" rows="2" required maxlength="255" placeholder="Contoh: Jl. Merdeka No. 10, Bandung" class="mt-2 w-full rounded-lg border border-slate-300 p-3 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100">{{ old('store_address', $store['store_address']) }}</textarea>
                </label>
                <label class="block text-sm font-semibold text-slate-700">Nomor HP / WhatsApp toko
                    <input name="store_phone" value="{{ old('store_phone', $store['store_phone']) }}" required maxlength="50" placeholder="Contoh: 0812-3456-7890" class="mt-2 w-full rounded-lg border border-slate-300 p-3 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </label>
                <label class="block text-sm font-semibold text-slate-700">Catatan kaki struk <span class="font-normal text-slate-400">(opsional)</span>
                    <input name="store_footer" value="{{ old('store_footer', $store['store_footer']) }}" maxlength="255" placeholder="Contoh: Terima kasih sudah berbelanja" class="mt-2 w-full rounded-lg border border-slate-300 p-3 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </label>
                <button class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Simpan profil toko
                </button>
            </form>
        </section>

        <section class="mx-auto w-full max-w-[330px]">
            <p class="mb-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-400">Pratinjau kop struk</p>
            <div class="rounded-t-xl bg-white px-6 pb-6 pt-6 text-center font-mono text-[13px] leading-relaxed text-slate-900 shadow-lg">
                <p class="text-base font-black tracking-wide">{{ $store['store_name'] }}</p>
                <p class="mt-1 text-[11px] text-slate-600">{{ $store['store_address'] }}</p>
                <p class="text-[11px] text-slate-600">WA: {{ $store['store_phone'] }}</p>
                <div class="mt-4 border-t-2 border-dashed border-slate-300 pt-3 text-[11px] text-slate-500">
                    <p>No. Struk: ORD-XXXXXX</p>
                    <p class="mt-2">{{ $store['store_footer'] }}</p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
