@extends('layouts.app')

@section('title', 'Edit Alat - Panel Admin')
@section('header-title', 'Edit Data Alat')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.alat.update', $alat->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nama Alat
            </label>

            <input type="text"
                   name="nama_alat"
                   value="{{ old('nama_alat', $alat->nama_alat) }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

            @error('nama_alat')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kategori
            </label>

            <div class="relative">
                <input type="search"
                       name="kategori"
                       id="kategori-search"
                       value="{{ old('kategori', $alat->kategori->nama_kategori ?? '') }}"
                       autocomplete="off"
                       required
                       placeholder="Ketik nama kategori..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div id="kategori-results" class="absolute z-10 hidden w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
            </div>
            <p id="kategori-search-status" class="mt-1 text-xs text-gray-500">Ketik minimal 2 karakter untuk mencari kategori.</p>

            @error('kategori')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Stok
                </label>

                <input type="number"
                       name="stok"
                       value="{{ old('stok', $alat->stok) }}"
                       min="0"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('stok')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Status Kondisi
                </label>

                <input type="text"
                       name="status_kondisi"
                       value="{{ old('status_kondisi', $alat->status_kondisi) }}"
                       required
                       placeholder="Contoh: Baik / Rusak Ringan"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('status_kondisi')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Deskripsi
            </label>

            <textarea name="deskripsi"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $alat->deskripsi) }}</textarea>

            @error('deskripsi')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Gambar Alat
                <span class="text-xs text-gray-400 font-normal">
                    (Biarkan kosong jika tidak ingin mengganti)
                </span>
            </label>

            @if($alat->gambar)
                <div class="mb-3">
                    <img src="{{ asset($alat->gambar) }}"
                         alt="Preview"
                         class="w-16 h-16 object-cover rounded-lg border">
                </div>
            @endif

            <input type="file"
                   name="gambar"
                   accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

            @error('gambar')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.alat.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Perbarui
            </button>
        </div>
    </form>
</div>

<script>
    const categories = @json($kategoris->pluck('nama_kategori')->values());
    const categorySearch = document.getElementById('kategori-search');
    const categoryResults = document.getElementById('kategori-results');
    const categorySearchStatus = document.getElementById('kategori-search-status');

    categorySearch.addEventListener('input', function () {
        const search = this.value.trim().toLowerCase();
        categoryResults.innerHTML = '';

        if (search.length < 2) {
            categoryResults.classList.add('hidden');
            categorySearchStatus.textContent = 'Ketik minimal 2 karakter untuk mencari kategori.';
            return;
        }

        const matches = categories.filter(category =>
            category.toLowerCase().includes(search)
        ).slice(0, 10);

        if (matches.length === 0) {
            categoryResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Kategori tidak ditemukan.</div>';
        } else {
            matches.forEach(category => {
                const result = document.createElement('button');
                result.type = 'button';
                result.className = 'block w-full px-3 py-2 text-left text-sm hover:bg-blue-50';
                result.textContent = category;
                result.addEventListener('click', function () {
                    categorySearch.value = category;
                    categoryResults.classList.add('hidden');
                    categorySearchStatus.textContent = 'Kategori terpilih.';
                });
                categoryResults.appendChild(result);
            });
        }

        categoryResults.classList.remove('hidden');
        categorySearchStatus.textContent = matches.length ? 'Pilih kategori dari hasil pencarian.' : 'Kategori tidak ditemukan.';
    });

    document.addEventListener('click', function (event) {
        if (!categorySearch.contains(event.target) && !categoryResults.contains(event.target)) {
            categoryResults.classList.add('hidden');
        }
    });
</script>
@endsection