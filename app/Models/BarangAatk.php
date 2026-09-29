<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangAatk extends Model
{
    use HasFactory;

    protected $table = 'barang_aatk';

    protected $fillable = [
        'kode_aatk',
        'nama_barang',
        'kategori',
        'stok',
        'satuan',
        'foto',
    ];

    /**
     * Status ketersediaan otomatis dihitung dari jumlah stok.
     * Habis   : stok 0
     * Menipis : stok 1 - 15
     * Tersedia: stok > 15
     */
    public function getStatusAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'Habis';
        }

        if ($this->stok <= 10) {
            return 'Menipis';
        }

        return 'Tersedia';
    }

    public static function generateKode(): string
    {
        $terakhir = self::orderBy('id', 'desc')->first();
        $nomorBerikutnya = $terakhir ? ((int) substr($terakhir->kode_aatk, 4)) + 1 : 1;
        return 'ATK-' . str_pad($nomorBerikutnya, 3, '0', STR_PAD_LEFT);
    }

        public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'barang_aatk_id');
    }
}