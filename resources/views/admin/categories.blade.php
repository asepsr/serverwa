<x-layouts.admin title="Kategori">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Master kategori</h2>
            <p class="mt-1 text-sm text-slate-500">Kategori ini dipakai sebagai dropdown saat membuat produk.</p>
        </div>
        <button onclick="openCategoryModal('category-create-modal')" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Tambah Kategori
        </button>
    </div>

    <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
        <table class="w-full min-w-[640px] text-left text-sm" id="categoriesTable">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Deskripsi</th><th class="px-4 py-3">Produk</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr class="border-b border-slate-100 hover:bg-teal-50/40">
                        <td class="px-4 py-4 font-semibold text-slate-700">{{ $category->name }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $category->description ?: '-' }}</td>
                        <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $category->products_count }} produk</span></td>
                        <td class="px-4 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <button
                                    data-url="{{ route('admin.categories.update', $category) }}"
                                    data-name="{{ $category->name }}"
                                    data-description="{{ $category->description }}"
                                    data-active="{{ $category->is_active ? '1' : '0' }}"
                                    onclick="openEditCategoryModal(this)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-600 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700 active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Belum ada kategori. Klik "Tambah Kategori" untuk membuat yang pertama.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- Modal tambah --}}
    <div id="category-create-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/50" onclick="closeCategoryModal('category-create-modal')"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Tambah kategori</h3>
                <button onclick="closeCategoryModal('category-create-modal')" class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <input name="name" placeholder="Nama kategori" required maxlength="100" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500">
                <input name="description" placeholder="Deskripsi (opsional)" maxlength="255" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500">
                <label class="flex items-center gap-2 text-sm text-slate-600"><input name="is_active" type="checkbox" value="1" checked class="accent-teal-600"> Kategori aktif</label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeCategoryModal('category-create-modal')" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white hover:bg-teal-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal edit --}}
    <div id="category-edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/50" onclick="closeCategoryModal('category-edit-modal')"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Edit kategori</h3>
                <button onclick="closeCategoryModal('category-edit-modal')" class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">✕</button>
            </div>
            <form id="category-edit-form" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input id="edit-cat-name" name="name" placeholder="Nama kategori" required maxlength="100" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500">
                <input id="edit-cat-description" name="description" placeholder="Deskripsi (opsional)" maxlength="255" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500">
                <label class="flex items-center gap-2 text-sm text-slate-600"><input id="edit-cat-active" name="is_active" type="checkbox" value="1" class="accent-teal-600"> Kategori aktif</label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeCategoryModal('category-edit-modal')" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button class="rounded-lg bg-amber-500 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-600">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCategoryModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
        }
        function closeCategoryModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
        }
        function openEditCategoryModal(btn) {
            document.getElementById('category-edit-form').action = btn.dataset.url;
            document.getElementById('edit-cat-name').value = btn.dataset.name || '';
            document.getElementById('edit-cat-description').value = btn.dataset.description || '';
            document.getElementById('edit-cat-active').checked = btn.dataset.active === '1';
            openCategoryModal('category-edit-modal');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCategoryModal('category-create-modal');
                closeCategoryModal('category-edit-modal');
            }
        });
    </script>
</x-layouts.admin>
