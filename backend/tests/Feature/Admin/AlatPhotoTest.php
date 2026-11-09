<?php

namespace Tests\Feature\Admin;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AlatPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_remove_an_equipment_photo_while_updating_it(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $imagePath = 'storage/alat/' . Str::uuid() . '.jpg';
        $absoluteImagePath = public_path($imagePath);
        mkdir(dirname($absoluteImagePath), 0755, true);
        file_put_contents($absoluteImagePath, 'test image');

        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Proyektor',
            'stok' => 3,
            'status_kondisi' => 'Baik',
            'gambar' => $imagePath,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.alat.update', $alat), [
                'nama_alat' => 'Proyektor',
                'kategori' => 'Elektronik',
                'stok' => 3,
                'status_kondisi' => 'Baik',
                'hapus_gambar' => '1',
            ])
            ->assertRedirect(route('admin.alat.index'));

        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'gambar' => null,
        ]);
        $this->assertFileDoesNotExist($absoluteImagePath);
    }

    public function test_equipment_photo_is_preserved_when_removal_is_not_selected(): void
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
            'stok' => 3,
            'status_kondisi' => 'Baik',
            'gambar' => 'storage/alat/proyektor.jpg',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.alat.update', $alat), [
                'nama_alat' => 'Proyektor',
                'kategori' => 'Elektronik',
                'stok' => 3,
                'status_kondisi' => 'Baik',
            ])
            ->assertRedirect(route('admin.alat.index'));

        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'gambar' => 'storage/alat/proyektor.jpg',
        ]);
    }
}
