<?php

namespace App\Http\Controllers\Admin;

use App\Models\Alat;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait PengembalianActions
{
    // Arahkan URL lama ke halaman menunggu pengembalian.
    public function indexPengembalian(Request $request)
    {
        return redirect()->route('admin.pengembalian.menunggu', $request->query());
    }

    public function indexMenungguPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamanAktif = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.menunggu', compact('peminjamanAktif', 'search'));
    }

    public function indexRiwayatPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereHas('peminjaman.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })->orWhere('kondisi_kembali', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.riwayat', compact('pengembalians', 'search'));
    }

    // Admin memproses pengembalian baru (sama seperti petugas)
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

            if ($peminjaman->status !== 'dipinjam') {
                return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
            }

            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->denda ?? 0,
                'petugas_id'      => auth()->id(), // admin yang proses, tetap tercatat di kolom ini
            ]);

            $peminjaman->update(['status' => 'selesai']);

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Form edit data pengembalian yang sudah ada
    public function editPengembalian($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat'])->findOrFail($id);
        return view('admin.pengembalian.edit', compact('pengembalian'));
    }

    // Update kondisi/denda (koreksi data, TIDAK mengubah stok karena barang sudah kembali)
    public function updatePengembalian(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->update([
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $request->denda ?? 0,
        ]);

        return redirect()->route('admin.pengembalian.riwayat')->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    // Hapus data pengembalian -> rollback status peminjaman & stok alat
    public function destroyPengembalian($id)
    {
        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::with('peminjaman.detailPinjams')->findOrFail($id);
            $peminjaman = $pengembalian->peminjaman;

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->stok < $detail->jumlah) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak cukup untuk membatalkan pengembalian ini.");
                }
                $alat->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);
            $pengembalian->delete();

            DB::commit();
            return redirect()->route('admin.pengembalian.riwayat')->with('success', 'Data pengembalian dihapus, status peminjaman dikembalikan ke "Dipinjam".');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
