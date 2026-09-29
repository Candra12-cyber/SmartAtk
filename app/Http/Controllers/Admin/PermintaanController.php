<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangAatk;
use App\Models\Notifikasi;
use App\Models\Permintaan;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Daftar semua permintaan dari seluruh pegawai.
     */
    public function index(Request $request)
    {
        $query = Permintaan::with(['items', 'user']);

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nomor_permintaan', 'like', "%{$cari}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permintaan = $query->latest()->paginate(10)->withQueryString();

        return view('admin.permintaan-index', compact('permintaan'));
    }

    /**
     * Detail 1 permintaan untuk ditinjau admin.
     */
    public function show($id)
    {
        $permintaan = Permintaan::with(['items', 'user'])->findOrFail($id);

        return view('admin.permintaan-detail', compact('permintaan'));
    }

    /**
     * Setujui permintaan.
     *
     * INI YANG BERUBAH: sekarang membaca input 'disetujui[item_id]' dari form,
     * menyimpannya ke jumlah_disetujui per item, dan mengembalikan selisih
     * (jumlah diajukan - jumlah disetujui) ke stok.
     */
    public function setujui(Request $request, $id)
    {
        $permintaan = Permintaan::with('items')->findOrFail($id);

        if ($permintaan->status !== 'Menunggu') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'disetujui' => ['required', 'array'],
            'disetujui.*' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $permintaan) {
            foreach ($permintaan->items as $item) {
                $jumlahDisetujui = (int) ($request->input('disetujui')[$item->id] ?? 0);

                // Jangan sampai disetujui melebihi jumlah yang diajukan.
                $jumlahDisetujui = min($jumlahDisetujui, $item->jumlah);

                $selisih = $item->jumlah - $jumlahDisetujui;

                if ($selisih > 0) {
                    $barang = BarangAatk::where('nama_barang', $item->nama_barang)->first();
                    if ($barang) {
                        $barang->increment('stok', $selisih);

                        RiwayatStok::create([
                            'barang_aatk_id' => $barang->id,
                            'jenis' => 'masuk',
                            'jumlah' => $selisih,
                            'keterangan' => "Sebagian permintaan {$permintaan->nomor_permintaan} tidak disetujui",
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                $item->update(['jumlah_disetujui' => $jumlahDisetujui]);
            }

            $permintaan->update([
                'status' => 'Disetujui',
                'diproses_oleh' => auth()->id(),
            ]);
        });

        Notifikasi::buat(
            $permintaan->user_id,
            'Permintaan Disetujui',
            "Permintaan {$permintaan->nomor_permintaan} Anda telah disetujui.",
            'sukses',
            route('permintaan.show', $permintaan->id)
        );

        return back()->with('status', 'Permintaan berhasil disetujui.');
    }

    /**
     * Tolak permintaan dengan alasan.
     *
     * INI JUGA BERUBAH: kalau permintaan yang ditolak sudah pernah disetujui
     * sebagian (statusnya Disetujui/Diproses/Dikirim), stok yang dikembalikan
     * hanya sebesar jumlah_disetujui — bukan jumlah penuh — karena selisihnya
     * sudah dikembalikan lebih dulu saat approval.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate(['alasan_penolakan' => ['required', 'string']]);

        $permintaan = Permintaan::with('items')->findOrFail($id);

        if (in_array($permintaan->status, ['Menunggu', 'Disetujui', 'Diproses', 'Dikirim'])) {
            foreach ($permintaan->items as $item) {
                $jumlahTertahan = $permintaan->status === 'Menunggu'
                    ? $item->jumlah
                    : ($item->jumlah_disetujui ?? $item->jumlah);

                if ($jumlahTertahan > 0) {
                    $barang = BarangAatk::where('nama_barang', $item->nama_barang)->first();
                    if ($barang) {
                        $barang->increment('stok', $jumlahTertahan);

                        RiwayatStok::create([
                            'barang_aatk_id' => $barang->id,
                            'jenis' => 'masuk',
                            'jumlah' => $jumlahTertahan,
                            'keterangan' => "Pengembalian stok - Permintaan {$permintaan->nomor_permintaan} ditolak",
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        $permintaan->update([
            'status' => 'Ditolak',
            'catatan' => $permintaan->catatan . "\n\n[Alasan ditolak]: " . $request->alasan_penolakan,
            'diproses_oleh' => auth()->id(),
        ]);

        Notifikasi::buat(
            $permintaan->user_id,
            'Permintaan Ditolak',
            "Permintaan {$permintaan->nomor_permintaan} ditolak. Alasan: {$request->alasan_penolakan}",
            'gagal',
            route('permintaan.show', $permintaan->id)
        );

        return redirect()->route('admin.permintaan.index')->with('status', 'Permintaan ditolak.');
    }

    /**
     * Update status pengiriman (Diproses / Dikirim / Selesai).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:Diproses,Dikirim,Selesai'],
        ]);

        $permintaan = Permintaan::findOrFail($id);
        $permintaan->update([
            'status' => $request->status,
            'diproses_oleh' => auth()->id(),
        ]);

        Notifikasi::buat(
            $permintaan->user_id,
            'Status Permintaan Diperbarui',
            "Permintaan {$permintaan->nomor_permintaan} kini berstatus \"{$request->status}\".",
            'info',
            route('permintaan.show', $permintaan->id)
        );

        return back()->with('status', 'Status permintaan diperbarui.');
    }
}