<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | OrderBot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <style>
        .dataTables_wrapper { padding: 1rem 0; color: #334155; font-size: 0.8125rem; }
        .dataTables_wrapper .dt-layout-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 0 0 0.8rem; }
        .dataTables_wrapper .dt-layout-row:last-child { margin: 0.8rem 0 0; }
        .dataTables_wrapper .dt-length,
        .dataTables_wrapper .dt-search { color: #334155; }
        .dataTables_wrapper .dt-length select,
        .dataTables_wrapper .dt-search input { border: 1px solid #dbe2ea; border-radius: 0.45rem; background: white; padding: 0.48rem 0.65rem; outline: none; }
        .dataTables_wrapper .dt-length select { margin-right: 0.35rem; }
        .dataTables_wrapper .dt-search input { margin-left: 0.35rem; width: 10.5rem; }
        .dataTables_wrapper .dt-length select:focus,
        .dataTables_wrapper .dt-search input:focus { border-color: #14b8a6; box-shadow: 0 0 0 2px rgb(20 184 166 / 0.12); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0; width: 100% !important; }
        table.dataTable thead th { background: #f1f5f9; color: #334155; font-weight: 700; border-bottom: 1px solid #e2e8f0 !important; white-space: nowrap; }
        table.dataTable tbody tr:nth-child(even) { background: #f8fafc; }
        table.dataTable tbody tr:hover { background: #f0fdfa; }
        table.dataTable tbody td { border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .dataTables_wrapper .dt-info { color: #475569; padding-top: 0.45rem; }
        .dataTables_wrapper .dt-paging { display: flex; justify-content: flex-end; }
        .dataTables_wrapper .dt-paging-button { min-width: 2.1rem; border: 1px solid #dbe2ea !important; border-radius: 0 !important; background: white !important; color: #334155 !important; }
        .dataTables_wrapper .dt-paging-button:first-child { border-radius: 0.5rem 0 0 0.5rem !important; }
        .dataTables_wrapper .dt-paging-button:last-child { border-radius: 0 0.5rem 0.5rem 0 !important; }
        .dataTables_wrapper .dt-paging-button:hover { background: #f0fdfa !important; color: #0f766e !important; }
        .dataTables_wrapper .dt-paging-button.current { background: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #0f172a !important; font-weight: 700; }
        .dataTables_wrapper .dt-paging-button:disabled { cursor: not-allowed; opacity: 0.45; }
        @media (max-width: 640px) { .dataTables_wrapper .dt-layout-row { align-items: flex-start; flex-direction: column; } .dataTables_wrapper .dt-search input { width: 100%; } .dataTables_wrapper .dt-paging { justify-content: flex-start; } }
    </style>
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800">
    <div class="min-h-screen lg:grid lg:grid-cols-[17rem_minmax(0,1fr)]">
        <input id="admin-sidebar-toggle" type="checkbox" class="peer hidden">
        <aside class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full overflow-y-auto bg-slate-950 px-5 py-7 text-slate-300 transition-transform duration-200 peer-checked:translate-x-0 lg:static lg:z-auto lg:w-auto lg:translate-x-0 lg:overflow-visible">
            <div class="flex items-start justify-between border-b border-slate-800 pb-7">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-teal-400 text-lg font-black text-slate-950">O</span>
                <span><strong class="block text-base text-white">OrderBot</strong><small class="text-xs text-slate-500">Control center</small></span>
                </a>
                <label for="admin-sidebar-toggle" title="Tutup menu" class="grid h-8 w-8 cursor-pointer place-items-center rounded-lg text-slate-400 hover:bg-slate-900 hover:text-white lg:hidden">X</label>
            </div>
            <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Operasional</p>
            <nav class="mt-3 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.dashboard') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>Dashboard</a>
                <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.orders*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg>Pesanan</a>
            </nav>
            <p class="mt-8 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Master</p>
            <nav class="mt-3 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.products') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.products*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>Produk</a>
                <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.categories*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>Kategori</a>
                <a href="{{ route('admin.store-profile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.store-profile*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .414.336.75.75.75z" /></svg>Profil Toko</a>
            </nav>
            <p class="mt-8 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Pengaturan Bot</p>
            <nav class="mt-3 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.whatsapp') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.whatsapp*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm3.75 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm3.75 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>WhatsApp</a>
                <a href="{{ route('admin.qris') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.qris*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zm0 9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zm9.75-9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zm0 6.75c0-.621.504-1.125 1.125-1.125h1.5c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5zm0 3c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5z" /></svg>QRIS</a>
                <a href="{{ route('admin.bot-messages') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.bot-messages*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>Pesan Bot</a>
            </nav>
            <p class="mt-8 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Akses</p>
            <nav class="mt-3 space-y-1 text-sm font-medium"><a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.users*') ? 'bg-teal-400 text-slate-950 shadow-sm' : 'hover:bg-slate-900 hover:text-white' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>Pengguna</a></nav>
        </aside>
        <label for="admin-sidebar-toggle" class="fixed inset-0 z-30 hidden bg-slate-950/50 peer-checked:block lg:hidden" aria-label="Tutup menu"></label>
        <main class="min-w-0">
            <header class="flex min-h-20 items-center justify-between border-b border-slate-200 bg-white px-5 sm:px-8">
            <div class="flex items-center gap-3"><label for="admin-sidebar-toggle" title="Buka menu" class="grid h-9 w-9 cursor-pointer place-items-center rounded-lg border border-slate-200 text-lg font-bold text-slate-600 hover:bg-slate-100 lg:hidden">☰</label><div><p class="text-xs font-medium text-slate-400">Dashboard / {{ $title ?? 'Ringkasan' }}</p><h1 class="mt-1 text-xl font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</h1></div></div>
                <div class="relative" id="user-menu-wrap">
                    <button id="user-menu-btn" onclick="document.getElementById('user-menu').classList.toggle('hidden')" class="flex items-center gap-3 rounded-lg px-2 py-1.5 transition hover:bg-slate-50">
                        <div class="hidden text-right sm:block"><p class="text-sm font-semibold text-slate-800">{{ auth()->user()?->name }}</p><p class="text-xs capitalize text-slate-400">{{ auth()->user()?->role }}</p></div>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-teal-100 text-sm font-bold text-teal-700">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <div id="user-menu" class="absolute right-0 z-50 mt-2 hidden w-52 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                        <div class="border-b border-slate-100 px-4 py-3 sm:hidden"><p class="text-sm font-semibold text-slate-800">{{ auth()->user()?->name }}</p><p class="text-xs capitalize text-slate-400">{{ auth()->user()?->role }}</p></div>
                        <a href="{{ route('admin.store-profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-teal-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .414.336.75.75.75z" /></svg>Profil Toko</a>
                        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>Logout</button></form>
                    </div>
                </div>
                <script>
                    document.addEventListener('click', function (e) {
                        var wrap = document.getElementById('user-menu-wrap');
                        var menu = document.getElementById('user-menu');
                        if (wrap && menu && !wrap.contains(e.target)) menu.classList.add('hidden');
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') document.getElementById('user-menu')?.classList.add('hidden');
                    });
                </script>
            </header>
            <div class="mx-auto max-w-7xl p-5 sm:p-8">
                @if (session('success'))<div class="mb-5 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="mb-5 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>@endif
                @if (isset($errors) && $errors->any())<div class="mb-5 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
                <div class="animate-[fade-in_300ms_ease-out]">{{ $slot }}</div>
            </div>
        </main>
    </div>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        function initSimpleTable(table) {
            const rows = Array.from(table.tBodies[0]?.rows || []);
            let pageSize = 10;
            let filteredRows = rows;
            let page = 1;
            let sortColumn = -1;
            let sortDirection = 1;
            const wrapper = document.createElement('div');
            wrapper.className = 'dataTables_wrapper';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
            const controls = document.createElement('div');
            controls.className = 'dt-layout-row';
            controls.innerHTML = '<label class="dt-length"><select aria-label="Jumlah data per halaman"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select> data per halaman</label><label class="dt-search">Cari: <input type="search" placeholder=""></label>';
            wrapper.insertBefore(controls, table);
            const footer = document.createElement('div');
            footer.className = 'dt-layout-row';
            wrapper.appendChild(footer);
            const info = document.createElement('span');
            info.className = 'dt-info';
            const paging = document.createElement('div');
            paging.className = 'dt-paging';
            footer.append(info, paging);
            const search = controls.querySelector('input');
            const length = controls.querySelector('select');

            function render() {
                const start = (page - 1) * pageSize;
                const visible = filteredRows.slice(start, start + pageSize);
                rows.forEach((row) => { row.style.display = 'none'; });
                visible.forEach((row) => { row.style.display = ''; table.tBodies[0].appendChild(row); });
                const end = Math.min(start + visible.length, filteredRows.length);
                info.textContent = filteredRows.length ? `Menampilkan ${start + 1} sampai ${end} dari ${filteredRows.length} data` : 'Tidak ada data';
                paging.innerHTML = '';
                const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
                const previous = document.createElement('button');
                previous.className = 'dt-paging-button';
                previous.textContent = '‹';
                previous.disabled = page === 1;
                previous.addEventListener('click', () => { if (page > 1) { page -= 1; render(); } });
                paging.appendChild(previous);
                for (let number = 1; number <= totalPages; number += 1) {
                    const button = document.createElement('button');
                    button.className = `dt-paging-button${number === page ? ' current' : ''}`;
                    button.textContent = number;
                    button.addEventListener('click', () => { page = number; render(); });
                    paging.appendChild(button);
                }
                const next = document.createElement('button');
                next.className = 'dt-paging-button';
                next.textContent = '›';
                next.disabled = page === totalPages;
                next.addEventListener('click', () => { if (page < totalPages) { page += 1; render(); } });
                paging.appendChild(next);
            }

            length.addEventListener('change', () => { pageSize = Number(length.value); page = 1; render(); });
            search.addEventListener('input', () => {
                const query = search.value.toLowerCase();
                filteredRows = rows.filter((row) => row.textContent.toLowerCase().includes(query));
                page = 1;
                render();
            });
            table.querySelectorAll('thead th').forEach((header, column) => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    sortDirection = sortColumn === column ? sortDirection * -1 : 1;
                    sortColumn = column;
                    rows.sort((left, right) => left.cells[column].textContent.localeCompare(right.cells[column].textContent, 'id', { numeric: true }) * sortDirection);
                    filteredRows = rows;
                    render();
                });
            });
            render();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table[id$="Table"]').forEach(function (table) {
                try {
                    if (typeof DataTable === 'undefined') throw new Error('DataTables CDN unavailable');
                    new DataTable(table, {
                        pageLength: 10,
                        lengthMenu: [10, 25, 50],
                        language: {
                            search: 'Cari:',
                            lengthMenu: '_MENU_ data per halaman',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            infoEmpty: 'Tidak ada data',
                            zeroRecords: 'Data tidak ditemukan',
                            paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                        }
                    });
                } catch (error) {
                    initSimpleTable(table);
                }
            });
        });
    </script>
</body>
</html>
