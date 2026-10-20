<?php

namespace Tests\Feature\Admin;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_a_loan_must_be_processed_through_the_return_flow(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 10,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $admin->id,
            'tgl_pinjam' => '2026-10-01',
            'tgl_kembali_plan' => '2026-10-05',
            'status' => 'diajukan',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.peminjaman.updateStatus', $peminjaman), ['status' => 'dipinjam'])
            ->assertRedirect(route('admin.peminjaman.index'));

        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 8]);

        $this->put(route('admin.peminjaman.updateStatus', $peminjaman), ['status' => 'selesai'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 8]);
        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => 'dipinjam']);

        $this->post(route('admin.pengembalian.proses', $peminjaman), [
            'kondisi_kembali' => 'Baik',
            'denda' => 0,
        ])
            ->assertRedirect();

        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 10]);
        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => 'selesai']);
    }

    public function test_processing_a_return_as_lost_does_not_restore_equipment_stock(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 8,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $admin->id,
            'tgl_pinjam' => '2026-10-01',
            'tgl_kembali_plan' => '2026-10-05',
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.pengembalian.proses', $peminjaman), [
                'kondisi_kembali' => 'Hilang',
                'denda' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 8]);
        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Hilang',
        ]);

        $pengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->firstOrFail();
        $this->put(route('admin.pengembalian.update', $pengembalian), [
            'kondisi_kembali' => 'Baik',
            'denda' => 0,
        ])->assertRedirect(route('admin.pengembalian.riwayat'));
        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 10]);

        $this->put(route('admin.pengembalian.update', $pengembalian), [
            'kondisi_kembali' => 'Hilang',
            'denda' => 0,
        ])->assertRedirect(route('admin.pengembalian.riwayat'));
        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 8]);

        $this->delete(route('admin.pengembalian.destroy', $pengembalian))
            ->assertRedirect(route('admin.pengembalian.riwayat'));

        $this->assertDatabaseHas('alat', ['id' => $alat->id, 'stok' => 8]);
    }

    public function test_deleting_equipment_preserves_loan_details(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 10,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $admin->id,
            'tgl_pinjam' => '2026-10-01',
            'tgl_kembali_plan' => '2026-10-05',
            'status' => 'diajukan',
        ]);
        $detail = DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);

        $alat->delete();

        $this->assertDatabaseHas('detail_pinjam', [
            'id' => $detail->id,
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => null,
            'jumlah' => 2,
        ]);
    }

    public function test_status_of_completed_loan_with_deleted_equipment_cannot_be_changed(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 10,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $admin->id,
            'tgl_pinjam' => '2026-10-01',
            'tgl_kembali_plan' => '2026-10-05',
            'status' => 'selesai',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);
        $alat->delete();

        $this->actingAs($admin)
            ->put(route('admin.peminjaman.updateStatus', $peminjaman), ['status' => 'dipinjam'])
            ->assertRedirect(route('admin.peminjaman.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);
    }

    public function test_active_loans_cannot_be_deleted(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);

        foreach (['dipinjam', 'telat'] as $status) {
            $alat = Alat::create([
                'kategori_id' => $kategori->id,
                'nama_alat' => 'Proyektor ' . $status,
                'stok' => 8,
                'status_kondisi' => 'Baik',
            ]);
            $peminjaman = Peminjaman::create([
                'user_id' => $admin->id,
                'tgl_pinjam' => '2026-10-01',
                'tgl_kembali_plan' => '2026-10-05',
                'status' => $status,
            ]);
            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alat->id,
                'jumlah' => 2,
            ]);

            $this->actingAs($admin)
                ->delete(route('admin.peminjaman.destroy', $peminjaman))
                ->assertRedirect(route('admin.peminjaman.index'))
                ->assertSessionHas('error', 'Peminjaman yang masih aktif tidak dapat dihapus.');

            $this->assertDatabaseHas('peminjaman', [
                'id' => $peminjaman->id,
                'status' => $status,
            ]);
            $this->assertDatabaseHas('alat', [
                'id' => $alat->id,
                'stok' => 8,
            ]);
        }
    }
}
