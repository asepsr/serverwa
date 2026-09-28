<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | OrderBot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-900">
    <div class="flex min-h-screen">
        <aside class="shrink-0 bg-stone-950 p-5 text-stone-300 transform transition-transform duration-300 -translate-x-full sm:block">
            <button onclick="toggleSidebar()" class="absolute top-5 left-5 text-stone-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-stone-300" aria-label="Buka menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="stroke-2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="mb-10 block text-xl font-semibold text-white">OrderBot</a>
            <nav class="space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500 text-stone-950' : 'hover:bg-stone-800' }}">Ringkasan</a>
                <a href="{{ route('admin.orders') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.orders*') ? 'bg-emerald-500 text-stone-950' : 'hover:bg-stone-800' }}">Pesanan</a>
                <a href="{{ route('admin.products') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.products*') ? 'bg-emerald-500 text-stone-950' : 'hover:bg-stone-800' }}">Produk</a>
                <a href="{{ route('admin.users') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.users*') ? 'bg-emerald-500 text-stone-950' : 'hover:bg-stone-800' }}">Pengguna</a>
            </nav>
        </aside>
<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('translate-x-0');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>
        <main class="min-w-0 flex-1">
            <header class="flex items-center justify-between border-b border-stone-200 bg-white px-5 py-4 sm:px-8">
                <div><p class="text-xs font-medium uppercase text-stone-500">Administrasi</p><h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1></div>
                <div class="flex items-center gap-3"><span class="hidden text-sm text-stone-600 sm:inline">{{ auth()->user()->name }}</span><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="text-sm font-medium text-stone-600 hover:text-rose-700">Keluar</button></form></div>
            </header>
            <div class="p-5 sm:p-8">
                @if (session('success'))<div class="mb-5 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                @if ($errors->any())<div class="mb-5 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
                {{ $slot }}
            </div>
        </main>
</div>
    </div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#productsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/id.json'
            }
        });
        $('#ordersTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/id.json'
            }
        });
        $('#dashboardTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/id.json'
            }
        });
        $('#usersTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/id.json'
            }
        });
    });
</script>
</body>
</html>
