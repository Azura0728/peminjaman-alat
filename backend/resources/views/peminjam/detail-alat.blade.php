@extends('layouts.peminjam')

@section('title', $alat->nama_alat . ' - Detail Alat')

@section('content')
<div class="mb-4">
    <a href="{{ route('peminjam.katalog') }}" class="text-sm text-orange-600 hover:text-orange-700 font-semibold">
        &larr; Kembali ke Katalog
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($alat->gambar)
                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
            @else
                <span class="text-6xl">🛠️</span>
            @endif
        </div>

        <div class="p-6 flex flex-col">
            <p class="text-sm text-gray-500">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $alat->nama_alat }}</h1>

            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="bg-emerald-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Stok tersedia</p>
                    <p class="text-lg font-bold text-emerald-700">{{ $alat->stok }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Kondisi</p>
                    <p class="text-lg font-bold text-gray-700">{{ $alat->status_kondisi }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h2 class="text-sm font-semibold text-gray-700">Deskripsi</h2>
                <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $alat->deskripsi ?: 'Tidak ada deskripsi untuk alat ini.' }}</p>
            </div>

            <form action="{{ route('peminjam.ajukan') }}" method="POST" class="mt-6 border-t border-gray-200 pt-5">
                @csrf
                <input type="hidden" name="alat_id" value="{{ $alat->id }}">

                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah</label>
                        <input id="jumlah" type="number" name="jumlah" value="1" min="1" max="{{ $alat->stok }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="tgl_kembali_plan" class="block text-sm font-semibold text-gray-700 mb-1">Rencana Tanggal Kembali</label>
                        <input id="tgl_kembali_plan" type="date" name="tgl_kembali_plan" min="{{ now()->addDay()->format('Y-m-d') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Ajukan Pinjam
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
