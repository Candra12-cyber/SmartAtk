<?php

namespace App\Http\Controllers;

use App\Models\BarangAatk;
use Illuminate\Http\Request;

class AatkController extends Controller
{
    /**
     * Halaman katalog AATK untuk pegawai.
     */
    public function index(Request $request)
    {
        $query = BarangAatk::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_barang', 'like', "%{$cari}%")
                  ->orWhere('kode_aatk', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'Habis' => $query->where('stok', '<=', 0),
                'Menipis' => $query->whereBetween('stok', [1, 15]),
                'Tersedia' => $query->where('stok', '>', 15),
                default => null,
            };
        }

        $aatk = $query->orderBy('nama_barang')->paginate(8)->withQueryString();

        return view('User.aatk-index', compact('aatk'));
    }
}