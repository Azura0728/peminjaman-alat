@extends('layouts.peminjam')

@section('title', 'Katalog Alat')

@section('content')
<h2 class="text-lg font-bold text-gray-800 mb-4">Katalog Alat Tersedia</h2>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($alats as $alat)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition flex flex-col">
        <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($alat->gambar)
                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
            @else
                <span class="text-4xl">🛠️</span>
            @endif
        </div>
        <div class="p-3 flex-1 flex flex-col">
            <p class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $alat->nama_alat }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">Stok: {{ $alat->stok }}</span>
            </div>
            <button type="button" onclick="document.getElementById('modal-{{ $alat->id }}').classList.remove('hidden')"
                class="mt-3 w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2 rounded-lg transition">
                Ajukan Pinjam
            </button>
        </div>
    </div>

    <!-- Modal Ajukan Peminjaman -->
    <div id="modal-{{ $alat->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
            <h4 class="font-bold text-gray-800 mb-1">{{ $alat->nama_alat }}</h4>
            <p class="text-xs text-gray-500 mb-4">Stok tersedia: {{ $alat->stok }}</p>
            <form action="{{ route('peminjam.ajukan') }}" method="POST">
                @csrf
                <input type="hidden" name="alat_id" value="{{ $alat->id }}">

                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah</label>
                    <input type="number" name="jumlah" value="1" min="1" max="{{ $alat->stok }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Rencana Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali_plan" min="{{ now()->addDay()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-{{ $alat->id }}').classList.add('hidden')"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <p class="col-span-full text-center text-gray-500 py-10">Tidak ada alat yang tersedia saat ini.</p>
    @endforelse
</div>

<div class="mt-6">{{ $alats->links() }}</div>
@endsection