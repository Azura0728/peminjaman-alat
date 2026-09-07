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
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('peminjam.katalog') }}" class="text-white font-bold text-xl tracking-wide whitespace-nowrap">
                📦 AlatKu
            </a>

            <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex-1 max-w-xl">
                <div class="flex bg-white rounded-lg overflow-hidden">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat atau kategori..."
                        class="flex-1 px-4 py-2 text-sm text-gray-700 focus:outline-none">
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 px-4 text-white text-sm font-semibold">
                        Cari
                    </button>
                </div>
            </form>

            <nav class="flex items-center gap-4 whitespace-nowrap">
                <a href="{{ route('peminjam.riwayat') }}" class="text-white text-sm font-medium hover:underline flex items-center gap-1">
                    🧾 Pesanan Saya
                </a>
                <a href="{{ route('profil.edit') }}" class="text-white text-sm font-medium hover:underline flex items-center gap-1">
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
    <main class="max-w-7xl mx-auto px-4 py-6">
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