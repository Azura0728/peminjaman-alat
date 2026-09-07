@extends('layouts.peminjam')

@section('title', 'Pesanan Saya')

@section('content')
<h2 class="text-lg font-bold text-gray-800 mb-4">Pesanan Saya</h2>

<div class="flex gap-2 mb-4 border-b border-gray-200">
    @foreach(['semua' => 'Semua', 'diajukan' => 'Diajukan', 'dipinjam' => 'Dipinjam', 'selesai' => 'Selesai'] as $key => $label)
        <a href="{{ route('peminjam.riwayat', ['tab' => $key]) }}"
           class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $tab == $key ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="space-y-3">
    @forelse($peminjamans as $item)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex justify-between items-start mb-2">
            <span class="text-xs text-gray-500">{{ $item->tgl_pinjam }}</span>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                @if($item->status == 'diajukan') bg-yellow-100 text-yellow-800
                @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-800
                @elseif($item->status == 'selesai') bg-emerald-100 text-emerald-800
                @else bg-red-100 text-red-800 @endif">
                {{ ucfirst($item->status) }}
            </span>
        </div>

        <div class="border-t border-gray-100 pt-2">
            @foreach($item->detailPinjams as $d)
            <div class="flex justify-between items-center text-sm py-1">
                <span class="text-gray-800">{{ $d->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                <span class="text-gray-500">x{{ $d->jumlah }}</span>
            </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 mt-2 pt-2 flex justify-between items-center text-xs text-gray-500">
            <span>Rencana kembali: {{ $item->tgl_kembali_plan }}</span>

            @if($item->status == 'dipinjam')
                @if($item->permintaan_kembali)
                    <span class="text-blue-600 font-semibold">Menunggu diproses petugas</span>
                @else
                    <form action="{{ route('peminjam.pengembalian.ajukan', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Ajukan pengembalian alat ini? Segera bawa alat ke petugas.')"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                            Ajukan Pengembalian
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @empty
    <p class="text-center text-gray-500 py-10">Belum ada riwayat peminjaman.</p>
    @endforelse
</div>

<div class="mt-6">{{ $peminjamans->links() }}</div>
@endsection