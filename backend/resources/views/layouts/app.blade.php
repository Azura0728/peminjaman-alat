<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media print {
        .no-print { display: none !important; }
        aside, header { display: none !important; }
        main { padding: 0 !important; }
        .print-header { display: block !important; }
    }
    @media (max-width: 767px) {
        main table {
            display: block;
            width: 100%;
            overflow-x: auto;
        }
        main table th,
        main table td {
            white-space: nowrap;
        }
    }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen flex-col md:h-screen md:flex-row md:overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">

            <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                @if(auth()->user()->role === 'admin')
                    PANEL ADMIN
                @elseif(auth()->user()->role === 'petugas')
                    PANEL PETUGAS
                @else
                    PANEL
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-2">

                {{-- ================= MENU KHUSUS ADMIN ================= --}}
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('admin.alat.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.alat*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Alat
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola User
                    </a>

                    <a href="{{ route('admin.kategori.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.kategori*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Kategori
                    </a>

                    <a href="{{ route('admin.peminjaman.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.peminjaman*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Peminjaman
                    </a>

                    <details class="group" {{ request()->routeIs('admin.pengembalian*') ? 'open' : '' }}>
                        <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.pengembalian*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola Pengembalian
                            <span class="text-xs transition group-open:rotate-180">&#9662;</span>
                        </summary>
                        <div class="mt-1 space-y-1 pl-4">
                            <a href="{{ route('admin.pengembalian.menunggu') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.pengembalian.menunggu') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Menunggu Pengembalian</a>
                            <a href="{{ route('admin.pengembalian.riwayat') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.pengembalian.riwayat') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Riwayat Pengembalian</a>
                        </div>
                    </details>
                @endif

                {{-- ================= MENU KHUSUS PETUGAS ================= --}}
                @if(auth()->user()->role === 'petugas')
                    <a href="{{ route('petugas.dashboard') }}"
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.dashboard') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('petugas.peminjaman.index') }}"
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.peminjaman*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Persetujuan Peminjaman
                    </a>

                    <a href="{{ route('petugas.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.pengembalian*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Pemantauan Pengembalian
                    </a>

                    <a href="{{ route('petugas.laporan.index') }}"
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.laporan*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Cetak Laporan
                    </a>
                @endif
                
            </nav>
            <div class="px-4 pb-2">
                <a href="{{ route('profil.edit') }}"
                class="block px-4 py-2 rounded-lg transition text-sm {{ request()->routeIs('profil*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Profil Saya
                </a>
            </div>

            <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
                Logged in as:
                <span class="text-white font-semibold">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex min-w-0 flex-1 flex-col md:overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white shadow-sm min-h-16 flex flex-wrap items-center justify-between gap-3 px-4 py-3 md:flex-nowrap md:px-6 md:py-0 z-10">
                <div class="min-w-0 text-base font-semibold text-gray-800 md:text-lg">
                    @yield('header-title', 'Dashboard')
                </div>

                <div class="flex items-center gap-2">
                    <details class="relative md:hidden">
                        <summary class="cursor-pointer list-none rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                            Menu
                        </summary>
                        <nav class="absolute right-0 z-20 mt-2 max-h-[70vh] w-64 overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="{{ route('admin.alat.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola Alat</a>
                                <a href="{{ route('admin.users.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola User</a>
                                <a href="{{ route('admin.kategori.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola Kategori</a>
                                <a href="{{ route('admin.peminjaman.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola Peminjaman</a>
                                <a href="{{ route('admin.pengembalian.menunggu') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Menunggu Pengembalian</a>
                                <a href="{{ route('admin.pengembalian.riwayat') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Riwayat Pengembalian</a>
                            @elseif(auth()->user()->role === 'petugas')
                                <a href="{{ route('petugas.dashboard') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="{{ route('petugas.peminjaman.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Persetujuan Peminjaman</a>
                                <a href="{{ route('petugas.pengembalian.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Pemantauan Pengembalian</a>
                                <a href="{{ route('petugas.laporan.index') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Cetak Laporan</a>
                            @endif
                            <a href="{{ route('profil.edit') }}" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                        </nav>
                    </details>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="min-w-0 flex-1 p-4 md:p-6">
                @yield('content')
            </main>

        </div>

    </div>

</body>
</html>