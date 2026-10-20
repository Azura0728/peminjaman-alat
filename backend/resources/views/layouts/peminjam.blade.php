<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Alat')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- NAVBAR ATAS -->
    <header class="bg-orange-500 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between md:gap-4">
            <a href="{{ route('peminjam.katalog') }}" class="self-start text-white font-bold text-lg tracking-wide whitespace-nowrap md:text-xl">
                📦 Daftar Alat
            </a>

            <form action="{{ route('peminjam.katalog') }}" method="GET" class="w-full md:max-w-xl md:flex-1">
                <div class="flex bg-white rounded-lg overflow-hidden">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat atau kategori..."
                        class="min-w-0 flex-1 px-3 py-2 text-sm text-gray-700 focus:outline-none md:px-4">
                    <button type="submit" class="shrink-0 bg-orange-600 hover:bg-orange-700 px-4 text-white text-sm font-semibold">
                        Cari
                    </button>
                </div>
            </form>

            <nav class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm md:flex-nowrap md:gap-4 md:whitespace-nowrap">
                <a href="{{ route('peminjam.riwayat') }}" class="text-white text-sm font-medium hover:underline flex items-center gap-1">
                    🧾 Peminjaman Saya
                </a>
                <a href="{{ route('profil.edit') }}" class="max-w-full truncate text-white text-sm font-medium hover:underline flex items-center gap-1">
                    👤 {{ auth()->user()->name }}
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-orange-600 text-sm font-semibold px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                        Logout
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- KONTEN -->
    <main class="max-w-7xl mx-auto w-full min-w-0 px-4 py-4 md:py-6">
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</body>
</html>