@extends('layouts.app')

@section('title', 'Profil User - Panel Admin')
@section('header-title', 'Profil Pengguna')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
    <div class="flex items-center gap-4">
        @if($user->foto)
            <img src="{{ asset($user->foto) }}" alt="Foto {{ $user->name }}" class="w-16 h-16 rounded-full object-cover border">
        @else
            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
            <span class="inline-block mt-1 px-2.5 py-1 text-xs font-semibold rounded-full
                @if($user->role == 'admin') bg-purple-100 text-purple-800
                @elseif($user->role == 'petugas') bg-blue-100 text-blue-800
                @else bg-green-100 text-green-800 @endif">
                {{ ucfirst($user->role) }}
            </span>
        </div>
    </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-800">&larr; Kembali ke Daftar User</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm border-t border-gray-100 pt-4">
            <div>
                <p class="text-gray-500">Email</p>
                <p class="font-medium text-gray-800">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-500">No. HP</p>
                <p class="font-medium text-gray-800">{{ $user->no_hp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Tanggal Daftar</p>
                <p class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Peminjaman</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Kondisi/Denda</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($riwayatPeminjaman as $item)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b">
                            @foreach($item->detailPinjams as $d)
                                <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }} ({{ $d->jumlah }} pcs)</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                @if($item->status == 'diajukan') bg-yellow-100 text-yellow-800
                                @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-800
                                @elseif($item->status == 'selesai') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">
                            @if($item->pengembalian)
                                <span class="text-xs">{{ $item->pengembalian->kondisi_kembali }}</span><br>
                                <span class="text-xs text-gray-500">{{ $item->pengembalian->denda > 0 ? 'Rp ' . number_format($item->pengembalian->denda) : 'Tanpa denda' }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dikembalikan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-500">User ini belum pernah meminjam alat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 bg-gray-50">{{ $riwayatPeminjaman->links() }}</div>
    </div>
@endsection