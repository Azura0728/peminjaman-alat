@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Data Pengembalian')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="mb-4 text-sm text-gray-600">
        Peminjam: <strong>{{ $pengembalian->peminjaman->user->name ?? '-' }}</strong><br>
        Alat:
        @foreach($pengembalian->peminjaman->detailPinjams as $d)
            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }}</span>
        @endforeach
    </div>

    <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'] as $opt)
                    <option value="{{ $opt }}" {{ $pengembalian->kondisi_kembali == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda (Rp)</label>
            <input type="number" name="denda" value="{{ $pengembalian->denda }}" min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.riwayat') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection