<?php

namespace App\Http\Controllers;

use App\Models\BarangAatk;
use App\Models\Permintaan;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Form buat permintaan baru.
     */
    public function create()
    {
        $daftarAATK = BarangAatk::orderBy('nama_barang')
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->nama_barang,
                'stok' => $item->stok,
                'satuan' => $item->satuan,
            ]);

        // Barang yang sudah dipilih lewat halaman Keranjang
        $keranjang = collect(session('keranjang', []))
            ->map(function ($jumlah, $id) {
                $barang = BarangAatk::find($id);

                if (! $barang) {
                    return null;
                }

                return [
                    'nama' => $barang->nama_barang,
                    'jumlah' => min((int) $jumlah, max((int) $barang->stok, 1)),
                ];
            })
            ->filter()
            ->values();

        return view('User.permintaan.create', compact('daftarAATK', 'keranjang'));
    }

    /**
     * Simpan permintaan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang' => ['required', 'array', 'min:1'],
            'barang.*.nama' => ['required', 'string', 'exists:barang_aatk,nama_barang'],
            'barang.*.jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string'],
        ], [
            'barang.*.nama.exists' => 'Nama AATK yang dipilih tidak ditemukan dalam daftar. Silakan pilih dari daftar yang tersedia.',
        ]);

        $status = 'Menunggu';

        DB::transaction(function () use ($request, $status) {
            $permintaan = Permintaan::create([
                'user_id' => auth()->id(),
                'nomor_permintaan' => Permintaan::generateNomorPermintaan(),
                'tanggal' => now(),
                'catatan' => $request->input('catatan'),
                'status' => $status,
            ]);

            foreach ($request->input('barang') as $item) {
                if (! empty($item['nama'])) {
                    $jumlah = (int) ($item['jumlah'] ?? 1);
                    $permintaan->items()->create([
                        'nama_barang' => $item['nama'],
                        'jumlah' => $jumlah,
                        'satuan' => $item['satuan'] ?? 'Pcs',
                    ]);

                    // Kurangi stok barang AATK
                    $barang = BarangAatk::where('nama_barang', $item['nama'])->first();
                    if ($barang) {
                        $barang->stok = max(0, $barang->stok - $jumlah);
                        $barang->save();
                    }

                    RiwayatStok::create([
                            'barang_aatk_id' => $barang->id,
                            'jenis' => 'keluar',
                            'jumlah' => $jumlah,
                            'keterangan' => "Permintaan {$permintaan->nomor_permintaan}",
                            'user_id' => auth()->id(),
                    ]);
                }
            }
        });

        // Keranjang sudah dipindahkan jadi permintaan, kosongkan lagi
        session()->forget('keranjang');

        $pesan = 'Permintaan AATK berhasil diajukan.';

        $redirectRoute = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard';

        return redirect()->route($redirectRoute)->with('status', $pesan);
    }

    /**
     * Daftar riwayat permintaan milik user yang login.
     */
    public function index(Request $request)
    {
        $query = Permintaan::where('user_id', auth()->id())->with('items');

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nomor_permintaan', 'like', "%{$cari}%")
                  ->orWhere('catatan', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permintaan = $query->latest()->paginate(10)->withQueryString();

        return view('User.permintaan-saya', compact('permintaan'));
    }

    /**
     * Detail 1 permintaan.
     */
    public function show($id)
    {
        $permintaan = Permintaan::with(['items', 'user'])->findOrFail($id);

        return view('User.permintaan.detail', compact('permintaan'));
    }
}