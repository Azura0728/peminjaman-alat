@extends('layouts.app')

@section('title', 'Cetak Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Transaksi Pengembalian')

@section('content')
<div class="no-print bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <form action="{{ route('petugas.laporan.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg">Filter</button>
        @if($tglAwal || $tglAkhir)
            <a href="{{ route('petugas.laporan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 text-sm rounded-lg">Reset</a>
        @endif
        <button type="button" onclick="window.print()" class="ml-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-lg">
            🖨️ Cetak Laporan
        </button>
    </form>
</div>

<div id="area-cetak" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="text-center mb-6 print-header hidden print:block">
        <h2 class="text-lg font-bold">Laporan Transaksi Pengembalian Alat</h2>
        <p class="text-sm text-gray-600">
            Periode: {{ $tglAwal ? \Carbon\Carbon::parse($tglAwal)->format('d M Y') : 'Semua' }}
            s/d {{ $tglAkhir ? \Carbon\Carbon::parse($tglAkhir)->format('d M Y') : 'Sekarang' }}
        </p>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                <th class="py-3 px-4 border-b">Peminjam</th>
                <th class="py-3 px-4 border-b">Alat</th>
                <th class="py-3 px-4 border-b">Tgl Kembali</th>
                <th class="py-3 px-4 border-b">Kondisi</th>
                <th class="py-3 px-4 border-b">Denda</th>
                <th class="py-3 px-4 border-b">Diproses Oleh</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm">
            @forelse($laporan as $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 border-b font-medium">{{ $p->peminjaman->user->name ?? '-' }}</td>
                <td class="py-3 px-4 border-b">
                    @foreach($p->peminjaman->detailPinjams as $d)
                        <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }}</span>
                    @endforeach
                </td>
                <td class="py-3 px-4 border-b">{{ $p->tgl_kembali }}</td>
                <td class="py-3 px-4 border-b">{{ $p->kondisi_kembali }}</td>
                <td class="py-3 px-4 border-b">{{ $p->denda > 0 ? 'Rp ' . number_format($p->denda) : '-' }}</td>
                <td class="py-3 px-4 border-b">{{ $p->petugas->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-4 text-center text-gray-500">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="font-bold text-gray-800">
                <td colspan="4" class="py-3 px-4 border-t text-right">Total Denda:</td>
                <td colspan="2" class="py-3 px-4 border-t">Rp {{ number_format($totalDenda) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection