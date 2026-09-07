<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan yang menunggu persetujuan
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menyetujui peminjaman (ubah status & kurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->stok < $detail->jumlah) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak peminjaman (hapus pengajuan agar peminjam bisa ajukan ulang)
    public function tolakPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                return redirect()->back()->with('error', 'Status pengajuan sudah berubah, tidak bisa ditolak.');
            }

            // Hapus detail dulu untuk hindari foreign key constraint error
            $peminjaman->detailPinjams()->delete();
            $peminjaman->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Memproses pengembalian alat (dari modul sebelumnya - JANGAN DIHAPUS)
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->denda ?? 0,
                'petugas_id'      => auth()->id(),
            ]);

            $peminjaman->update(['status' => 'selesai']);

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexPengembalian(Request $request)
{
    $searchAktif = $request->input('search_aktif');
    $searchRiwayat = $request->input('search_riwayat');

    $peminjamanAktif = Peminjaman::with(['user', 'detailPinjams.alat'])
        ->where('status', 'dipinjam')
        ->when($searchAktif, function ($query, $searchAktif) {
            return $query->whereHas('user', function ($q) use ($searchAktif) {
                $q->where('name', 'like', "%{$searchAktif}%");
            });
        })
        ->latest()
        ->paginate(10, ['*'], 'page_aktif')
        ->withQueryString();

    $riwayatPengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas'])
        ->when($searchRiwayat, function ($query, $searchRiwayat) {
            return $query->whereHas('peminjaman.user', function ($q) use ($searchRiwayat) {
                $q->where('name', 'like', "%{$searchRiwayat}%");
            })->orWhere('kondisi_kembali', 'like', "%{$searchRiwayat}%");
        })
        ->latest()
        ->paginate(10, ['*'], 'page_riwayat')
        ->withQueryString();

    return view('petugas.pengembalian.index', compact('peminjamanAktif', 'riwayatPengembalian', 'searchAktif', 'searchRiwayat'));
}

// Dashboard ringkasan untuk petugas
    public function dashboard()
    {
        $totalMenunggu = Peminjaman::where('status', 'diajukan')->count();
        $totalDipinjam = Peminjaman::where('status', 'dipinjam')->count();

        $totalPengembalianBulanIni = Pengembalian::whereMonth('tgl_kembali', now()->month)
            ->whereYear('tgl_kembali', now()->year)
            ->count();

        $totalDendaBulanIni = Pengembalian::whereMonth('tgl_kembali', now()->month)
            ->whereYear('tgl_kembali', now()->year)
            ->sum('denda');

        $peminjamanTerbaru = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalMenunggu', 'totalDipinjam', 'totalPengembalianBulanIni', 'totalDendaBulanIni', 'peminjamanTerbaru'
        ));
    }

    // Laporan transaksi pengembalian (filter periode + siap cetak)
    public function laporan(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas']);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tgl_kembali', [$tglAwal . ' 00:00:00', $tglAkhir . ' 23:59:59']);
        }

        $laporan = $query->latest()->get();
        $totalDenda = $laporan->sum('denda');

        return view('petugas.laporan.index', compact('laporan', 'tglAwal', 'tglAkhir', 'totalDenda'));
    }
}