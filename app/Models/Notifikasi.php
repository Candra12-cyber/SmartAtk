<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'url',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk membuat notifikasi baru bagi seorang user.
     * Contoh pemakaian:
     * Notifikasi::buat($userId, 'Permintaan Disetujui', 'Permintaan REQ-001 Anda telah disetujui.', 'sukses', route('permintaan.show', $id));
     */
    public static function buat(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): self
    {
        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
        ]);
    }
}