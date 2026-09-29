<?php

namespace Database\Seeders;

use App\Models\BarangAatk;
use Illuminate\Database\Seeder;

class BarangAatkSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['kode_aatk' => 'ATK-001', 'nama_barang' => 'Kertas HVS A4 80gr', 'kategori' => 'Kertas & Buku', 'stok' => 150, 'satuan' => 'Rim'],
            ['kode_aatk' => 'ATK-042', 'nama_barang' => 'Bolpoin Hitam Standard', 'kategori' => 'Alat Tulis Kantor', 'stok' => 12, 'satuan' => 'Kotak'],
            ['kode_aatk' => 'TNT-036', 'nama_barang' => 'Toner Printer Laser Hitam', 'kategori' => 'Tinta & Toner', 'stok' => 0, 'satuan' => 'Unit'],
            ['kode_aatk' => 'ATK-088', 'nama_barang' => 'Map Plastik Snelhecter', 'kategori' => 'Alat Tulis Kantor', 'stok' => 45, 'satuan' => 'Lusin'],
            ['kode_aatk' => 'ATK-015', 'nama_barang' => 'Tinta Printer Epson 003 Black', 'kategori' => 'Tinta & Toner', 'stok' => 30, 'satuan' => 'Botol'],
            ['kode_aatk' => 'ATK-023', 'nama_barang' => 'Spidol Whiteboard Hitam', 'kategori' => 'Alat Tulis Kantor', 'stok' => 8, 'satuan' => 'Box'],
            ['kode_aatk' => 'ATK-030', 'nama_barang' => 'Buku Folio 100 Lembar', 'kategori' => 'Kertas & Buku', 'stok' => 60, 'satuan' => 'Pcs'],
            ['kode_aatk' => 'ATK-055', 'nama_barang' => 'Stapler Sedang', 'kategori' => 'Perlengkapan Meja', 'stok' => 0, 'satuan' => 'Pcs'],
        ];

        foreach ($data as $item) {
            BarangAatk::create($item);
        }
    }
}