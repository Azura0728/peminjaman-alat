<?php

namespace App\Http\Controllers\Admin;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait PeminjamanActions
{
    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get(); // Atau ambil semua user jika bebas
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan', // Status awal
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            // Kurangi stok alat jika status langsung disetujui/dipinjam (opsional, atau dikurangi saat status berubah jadi 'dipinjam')

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman (Misal: dari diajukan -> dipinjam / selesai)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,telat',
        ]);

        DB::beginTransaction();

        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            // Logika pengelolaan stok otomatis
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                // Kurangi stok karena barang resmi dipinjam
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = $detail->alat;

                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam.");
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && $statusBaru == 'selesai') {
                // Kembalikan stok karena barang sudah dikembalikan (selesai)
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);

        // Jika statusnya sedang dipinjam, kembalikan stok terlebih dahulu sebelum dihapus
        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()
            ->route('admin.peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
