<?php

namespace App\Http\Controllers;

use App\Models\BarangAatk;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Halaman review keranjang sebelum checkout / buat permintaan.
     */
    public function index()
    {
        $keranjang = session('keranjang', []); // [barang_aatk_id => jumlah]

        $items = collect($keranjang)
            ->map(function ($jumlah, $id) {
                $barang = BarangAatk::find($id);

                if (! $barang) {
                    return null;
                }

                return [
                    'id' => $barang->id,
                    'nama' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'stok' => $barang->stok,
                    'jumlah' => min((int) $jumlah, max((int) $barang->stok, 1)),
                ];
            })
            ->filter()
            ->values();

        return view('User.keranjang', compact('items'));
    }

    /**
     * Tambah 1 barang ke keranjang (dipanggil dari tombol "Keranjang" di Daftar ATK).
     */
    public function tambah($id)
    {
        $barang = BarangAatk::findOrFail($id);

        if ($barang->stok <= 0) {
            return back()->with('error', "Stok \"{$barang->nama_barang}\" sedang habis.");
        }

        $keranjang = session('keranjang', []);
        $jumlahSekarang = $keranjang[$id] ?? 0;

        if ($jumlahSekarang >= $barang->stok) {
            return back()->with('error', "Jumlah \"{$barang->nama_barang}\" di keranjang sudah mencapai batas stok yang tersedia.");
        }

        $keranjang[$id] = $jumlahSekarang + 1;
        session(['keranjang' => $keranjang]);

        return back()->with('status', "\"{$barang->nama_barang}\" ditambahkan ke keranjang.");
    }

    /**
     * Ubah jumlah barang di halaman keranjang.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $barang = BarangAatk::findOrFail($id);
        $keranjang = session('keranjang', []);

        if (array_key_exists($id, $keranjang) || array_key_exists((string) $id, $keranjang)) {
            $keranjang[$id] = min($request->jumlah, max((int) $barang->stok, 1));
            session(['keranjang' => $keranjang]);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Jumlah barang diperbarui.');
    }

    /**
     * Hapus 1 barang dari keranjang.
     */
    public function hapus(Request $request, $id)
    {
        $keranjang = session('keranjang', []);
        unset($keranjang[$id]);
        session(['keranjang' => $keranjang]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Barang dihapus dari keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function kosongkan()
    {
        session()->forget('keranjang');

        return back()->with('status', 'Keranjang dikosongkan.');
    }
}