<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai sudah dibaca, lalu arahkan ke tujuannya.
     */
    public function baca($id)
    {
        $notif = Notifikasi::where('user_id', auth()->id())->findOrFail($id);
        $notif->update(['dibaca' => true]);

        return $notif->url ? redirect($notif->url) : back();
    }

    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function bacaSemua()
    {
        auth()->user()->notifikasi()->where('dibaca', false)->update(['dibaca' => true]);

        return back();
    }
}