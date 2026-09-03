<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [ 
            ['nama_kategori' => 'Jaringan & Konektivitas'], 
            ['nama_kategori' => 'Multimedia & Audio Visual'], 
            ['nama_kategori' => 'Perangkat Pemrosesan'], 
            ['nama_kategori' => 'Perkakas & Elektronik'], 
            ['nama_kategori' => 'Suku Cadang & Aksesoris'], 
        ];

        foreach ($kategori as $item) {
            Kategori::create($item);
        }
    }
}
