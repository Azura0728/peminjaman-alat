@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Kelola Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- BAGIAN 1: Peminjaman aktif yang bisa diproses pengembaliannya -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Menunggu Pengembalian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamanAktif as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b font-medium">{{ $item->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            @foreach($item->detailPinjams as $d)
                                <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }} ({{ $d->jumlah }} pcs)</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <button type="button" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold"
                                onclick="document.getElementById('modal-{{ $item->id }}').classList.remove('hidden')">
                                Proses Pengembalian
                            </button>

                            <!-- Modal sederhana -->
                            <div id="modal-{{ $item->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                                <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                                    <h4 class="font-bold text-gray-800 mb-4">Pengembalian - {{ $item->user->name ?? '-' }}</h4>
                                    <form action="{{ route('admin.pengembalian.proses', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Alat</label>
                                            <select name="kondisi_kembali" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Denda (Rp)</label>
                                            <input type="number" name="denda" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="document.getElementById('modal-{{ $item->id }}').classList.add('hidden')"
                                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm">Batal</button>
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">Tidak ada peminjaman yang menunggu dikembalikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- BAGIAN 2: Riwayat pengembalian + edit/hapus -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Pengembalian</h3>
            <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam/kondisi..."
                    class="px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                        <th class="py-3 px-4 border-b">Diproses Oleh</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($pengembalians as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b font-medium">{{ $p->peminjaman->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            @foreach($p->peminjaman->detailPinjams as $d)
                                <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }}</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">{{ $p->tgl_kembali }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                @if($p->kondisi_kembali == 'Baik') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $p->kondisi_kembali }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">{{ $p->denda > 0 ? 'Rp ' . number_format($p->denda) : '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $p->petugas->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.pengembalian.edit', $p->id) }}"
                                   class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold">Edit</a>
                                <form action="{{ route('admin.pengembalian.destroy', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus data ini? Status peminjaman akan kembali ke Dipinjam dan stok akan dikurangi lagi.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 text-center text-gray-500">Belum ada data pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 bg-gray-50">{{ $pengembalians->links() }}</div>
    </div>
@endsection