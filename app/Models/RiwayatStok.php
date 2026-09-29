<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';

    protected $fillable = [
        'barang_aatk_id',
        'jenis',
        'jumlah',
        'keterangan',
        'user_id',
    ];

    public function barang()
    {
        return $this->belongsTo(BarangAatk::class, 'barang_aatk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}