<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    public function katalogAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('peminjam.katalog', compact('alats', 'search'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'alat_id'          => 'required|exists:alat,id',
            'jumlah'           => 'required|integer|min:1',
            'tgl_kembali_plan' => 'required|date|after:today',
        ]);

        DB::beginTransaction();
        try {
            $alat = Alat::findOrFail($request->alat_id);

            if ($alat->stok < $request->jumlah) {
                return redirect()->back()->with('error', "Stok {$alat->nama_alat} tidak mencukupi.");
            }

            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id'       => $alat->id,
                'jumlah'        => $request->jumlah,
            ]);

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu persetujuan petugas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    public function riwayatPeminjaman(Request $request)
    {
        $tab = $request->input('tab', 'semua');

        $query = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id());

        if (in_array($tab, ['diajukan', 'dipinjam', 'selesai'])) {
            $query->where('status', $tab);
        }

        $peminjamans = $query->latest()->paginate(8)->withQueryString();

        return view('peminjam.riwayat', compact('peminjamans', 'tab'));
    }

    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status dipinjam.');
        }

        if ($peminjaman->permintaan_kembali) {
            return redirect()->back()->with('error', 'Permintaan pengembalian sudah diajukan sebelumnya.');
        }

        $peminjaman->update(['permintaan_kembali' => true]);

        return redirect()->back()->with('success', 'Permintaan pengembalian berhasil dikirim. Silakan bawa alat ke petugas.');
    }
}