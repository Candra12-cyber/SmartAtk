<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangAatk;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\StokBarangExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AatkController extends Controller
{
    /**
     * Daftar semua barang AATK.
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

        $aatk = $query->orderBy('nama_barang')->paginate(10)->withQueryString();

        return view('admin.aatk-index', compact('aatk'));
    }

    /**
     * Daftar kategori dasar (selalu tersedia sebagai saran) digabung dengan
     * kategori-kategori yang sudah pernah dipakai di database (dinamis),
     * supaya kategori baru yang pernah diketik user ikut muncul sebagai saran.
     */
    protected function daftarKategori(): array
    {
        $kategoriDasar = ['Alat Tulis Kantor', 'Kertas & Buku', 'Tinta & Toner', 'Perlengkapan Meja'];

        $kategoriDariDb = BarangAatk::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori')
            ->all();

        return collect($kategoriDasar)
            ->merge($kategoriDariDb)
            ->unique(fn ($k) => strtolower(trim($k)))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Form tambah barang baru.
     */
    public function create()
    {
        $kodeBerikutnya = BarangAatk::generateKode();
        $daftarKategori = $this->daftarKategori();

        return view('admin.aatk-form', compact('kodeBerikutnya', 'daftarKategori'));
    }

    /**
     * Simpan barang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    $sudahAda = BarangAatk::whereRaw('LOWER(TRIM(nama_barang)) = ?', [strtolower(trim($value))])->exists();

                    if ($sudahAda) {
                        $fail("Barang \"{$value}\" sudah ada di database. Gunakan tombol \"Tambah Stok\" pada barang tersebut kalau ingin menambah jumlah stoknya, atau gunakan nama lain kalau ini memang barang berbeda.");
                    }
                },
            ],
            'kategori' => ['required', 'string'],
            'stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
        ]);

        $data = [
            'kode_aatk' => BarangAatk::generateKode(),
            'nama_barang' => $request->nama_barang,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
        ];

        BarangAatk::create($data);

        return redirect()->route('admin.aatk.index')->with('status', 'Barang berhasil ditambahkan.');
    }

    /**
     * Form edit barang.
     */
    public function edit($id)
    {
        $barang = BarangAatk::findOrFail($id);
        $daftarKategori = $this->daftarKategori();

        return view('admin.aatk-form', compact('barang', 'daftarKategori'));
    }

    /**
     * Update data barang.
     */
    public function update(Request $request, $id)
    {
        $barang = BarangAatk::findOrFail($id);

        $request->validate([
            'nama_barang' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($barang) {
                    $sudahAda = BarangAatk::whereRaw('LOWER(TRIM(nama_barang)) = ?', [strtolower(trim($value))])
                        ->where('id', '!=', $barang->id)
                        ->exists();

                    if ($sudahAda) {
                        $fail("Barang \"{$value}\" sudah dipakai oleh data barang lain. Gunakan nama lain untuk menghindari duplikat.");
                    }
                },
            ],
            'kategori' => ['required', 'string'],
            'stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
        ]);

        $data = $request->only(['nama_barang', 'kategori', 'stok', 'satuan']);

        $barang->update($data);

        return redirect()->route('admin.aatk.index')->with('status', 'Barang berhasil diperbarui.');
    }

    /**
     * Hapus barang.
     */
    public function destroy($id)
    {
        $barang = BarangAatk::findOrFail($id);

        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        $barang->delete();

        return redirect()->route('admin.aatk.index')->with('status', 'Barang berhasil dihapus.');
    }

    public function exportExcel()
    {
        return Excel::download(new StokBarangExport, 'data-barang-atk-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdf()
    {
        $daftarBarang = BarangAatk::orderBy('nama_barang')->get();

        $pdf = Pdf::loadView('admin.aatk-pdf', compact('daftarBarang'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('data-barang-atk-' . now()->format('Ymd') . '.pdf');
    }

        /**
     * Tambah stok barang (barang masuk) dan catat riwayatnya.
     */
    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $barang = BarangAatk::findOrFail($id);
        $barang->increment('stok', $request->jumlah);

        RiwayatStok::create([
            'barang_aatk_id' => $barang->id,
            'jenis' => 'masuk',
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'sumber' => 'manual',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.aatk.index')->with('status', "Stok {$barang->nama_barang} berhasil ditambahkan.");
    }
}