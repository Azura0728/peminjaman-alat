@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header-title', 'Dashboard Petugas')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
        <p class="text-2xl font-bold text-amber-600">{{ $totalMenunggu }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Alat Sedang Dipinjam</p>
        <p class="text-2xl font-bold text-blue-600">{{ $totalDipinjam }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Pengembalian Bulan Ini</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $totalPengembalianBulanIni }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Denda Terkumpul Bulan Ini</p>
        <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalDendaBulanIni) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Pengajuan Terbaru Menunggu Persetujuan</h3>
        <a href="{{ route('petugas.peminjaman.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Alat</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamanTerbaru as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 border-b font-medium">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-4 border-b">
                        @foreach($item->detailPinjams as $d)
                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }}</span>
                        @endforeach
                    </td>
                    <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="py-4 text-center text-gray-500">Tidak ada pengajuan baru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection