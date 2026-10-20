<?php

namespace Tests\Feature\Admin;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlatDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_equipment_with_an_active_loan(): void
    {
        [$admin, $alat] = $this->createEquipmentWithLoan('dipinjam');

        $this->actingAs($admin)
            ->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('error', 'Alat tidak dapat dihapus karena masih sedang dipinjam.');

        $this->assertDatabaseHas('alat', ['id' => $alat->id]);
    }

    public function test_admin_cannot_delete_equipment_with_an_overdue_loan(): void
    {
        [$admin, $alat] = $this->createEquipmentWithLoan('telat');

        $this->actingAs($admin)
            ->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('error', 'Alat tidak dapat dihapus karena masih sedang dipinjam.');

        $this->assertDatabaseHas('alat', ['id' => $alat->id]);
    }

    public function test_delete_action_is_disabled_for_equipment_with_an_active_loan(): void
    {
        [$admin] = $this->createEquipmentWithLoan('dipinjam');

        $this->actingAs($admin)
            ->get(route('admin.alat.index'))
            ->assertOk()
            ->assertSee('Tidak dapat dihapus karena sedang dipinjam')
            ->assertSee('disabled', false);
    }

    public function test_admin_can_delete_equipment_without_an_active_loan(): void
    {
        [$admin, $alat] = $this->createEquipmentWithLoan('diajukan');

        $this->actingAs($admin)
            ->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success', 'Data alat berhasil dihapus.');

        $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
    }

    private function createEquipmentWithLoan(string $status): array
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $user = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password',
            'role' => 'peminjam',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 2,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'tgl_pinjam' => '2026-10-20',
            'tgl_kembali_plan' => '2026-10-21',
            'status' => $status,
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        return [$admin, $alat];
    }
}
