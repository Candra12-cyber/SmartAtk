<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangAatk;
use App\Models\Permintaan;
use App\Models\PermintaanItem;
use App\Models\User;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\LaporanPermintaanExport;
use App\Exports\LaporanPermintaanDetailExport;
use App\Exports\LaporanBarangMasukExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    protected function getRentangTanggal(Request $request): array
    {
        $dari = $request->filled('dari')
            ? Carbon::parse($request->dari)->startOfDay()
            : now()->startOfMonth();

        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->sampai)->endOfDay()
            : now()->endOfDay();

        return [$dari, $sampai];
    }

    /**
     * Query dasar permintaan sesuai filter tanggal, status, unit kerja.
     */
    protected function queryPermintaan(Request $request, Carbon $dari, Carbon $sampai)
    {
        $query = Permintaan::whereBetween('tanggal', [$dari, $sampai])->with(['items', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('unit_kerja')) {
            $query->whereHas('user', fn ($u) => $u->where('unit_kerja', $request->unit_kerja));
        }

        return $query;
    }

    protected function queryRiwayatMasuk(Request $request, Carbon $dari, Carbon $sampai)
    {
        $query = RiwayatStok::where('jenis', 'masuk')
            ->whereBetween('created_at', [$dari, $sampai])
            ->with(['barang', 'user']);

        if ($request->filled('cari_masuk')) {
            $cariMasuk = $request->cari_masuk;
            $query->whereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%{$cariMasuk}%"));
        }

        return $query;
    }

    /**
     * Hitung 4 kartu statistik (total permintaan, barang keluar, menunggu,
     * stok kritis) beserta persentase perubahan dari periode sebelumnya.
     */
    protected function hitungStatCards(Request $request, Carbon $dariObj, Carbon $sampaiObj, Collection $daftarPermintaan): array
    {
        $totalPermintaan = $daftarPermintaan->count();
        $totalBarangKeluar = $daftarPermintaan->sum(fn ($p) => $p->items->sum('jumlah'));
        $totalMenunggu = Permintaan::where('status', 'Menunggu')->count();
        $totalStokKritis = BarangAatk::where('stok', '<=', 15)->count();

        // Perbandingan dengan periode sebelumnya (durasi sama, mundur ke belakang)
        $durasiHari = $dariObj->diffInDays($sampaiObj) + 1;
        $dariSebelumnya = (clone $dariObj)->subDays($durasiHari);
        $sampaiSebelumnya = (clone $dariObj)->subDay()->endOfDay();

        $permintaanSebelumnya = Permintaan::whereBetween('tanggal', [$dariSebelumnya, $sampaiSebelumnya])->with('items')->get();
        $totalPermintaanSebelumnya = $permintaanSebelumnya->count();
        $totalBarangKeluarSebelumnya = $permintaanSebelumnya->sum(fn ($p) => $p->items->sum('jumlah'));

        $hitungDelta = function ($sekarang, $sebelumnya) {
            if ($sebelumnya <= 0) {
                return $sekarang > 0 ? 100 : 0;
            }
            return round((($sekarang - $sebelumnya) / $sebelumnya) * 100);
        };

        return [
            'totalPermintaan' => $totalPermintaan,
            'totalBarangKeluar' => $totalBarangKeluar,
            'totalMenunggu' => $totalMenunggu,
            'totalStokKritis' => $totalStokKritis,
            'deltaPermintaan' => $hitungDelta($totalPermintaan, $totalPermintaanSebelumnya),
            'deltaBarangKeluar' => $hitungDelta($totalBarangKeluar, $totalBarangKeluarSebelumnya),
        ];
    }

    /**
     * Jumlah permintaan per bulan, 6 bulan terakhir (buat grafik "Tren Penggunaan ATK").
     */
    protected function hitungTrenBulanan(): Collection
    {
        return collect(range(5, 0))->map(function ($mundur) {
            $bulan = now()->subMonths($mundur);
            $jumlah = Permintaan::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->count();

            return [
                'label' => $bulan->translatedFormat('M'),
                'jumlah' => $jumlah,
            ];
        });
    }

    /**
     * Daftar unit kerja unik, buat isi dropdown filter.
     */
    protected function ambilDaftarUnitKerja(): Collection
    {
        return User::whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct()
            ->orderBy('unit_kerja')
            ->pluck('unit_kerja');
    }

    /**
     * Detail transaksi (1 baris = 1 barang di 1 permintaan), dengan filter
     * kategori & pencarian, dipaginasi. Setiap baris tetap berupa objek
     * PermintaanItem (bukan array) supaya view bisa akses pakai "->".
     */
    protected function ambilDetailTransaksi(Request $request, Collection $permintaanIds): LengthAwarePaginator
    {
        $detailQuery = PermintaanItem::whereIn('permintaan_id', $permintaanIds)
            ->with(['permintaan.user']);

        if ($request->filled('kategori')) {
            $namaBarangDenganKategori = BarangAatk::where('kategori', $request->kategori)->pluck('nama_barang');
            $detailQuery->whereIn('nama_barang', $namaBarangDenganKategori);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $detailQuery->where(function ($q) use ($cari) {
                $q->where('nama_barang', 'like', "%{$cari}%")
                  ->orWhereHas('permintaan', fn ($p) => $p->where('nomor_permintaan', 'like', "%{$cari}%"));
            });
        }

        return $detailQuery->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($item) {
                // Ratakan struktur supaya gampang dipakai di view
                // (nomor_permintaan, tanggal, status dari permintaan induk).
                // Tetap objek PermintaanItem yang sama, cuma ditambah atribut baru.
                $item->nomor_permintaan = $item->permintaan->nomor_permintaan ?? '-';
                $item->tanggal = $item->permintaan->tanggal ?? now();
                $item->status = $item->permintaan->status ?? '-';
                $item->user = $item->permintaan->user ?? null;
                $item->permintaan_id = $item->permintaan->id ?? null;

                return $item;
            });
    }

    public function index(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);
        $dari = $dariObj->format('Y-m-d');
        $sampai = $sampaiObj->format('Y-m-d');

        $daftarPermintaan = $this->queryPermintaan($request, $dariObj, $sampaiObj)->get();
        $statCards = $this->hitungStatCards($request, $dariObj, $sampaiObj, $daftarPermintaan);

        $trenBulanan = $this->hitungTrenBulanan();
        $daftarUnitKerja = $this->ambilDaftarUnitKerja();

        $permintaanIds = $daftarPermintaan->pluck('id');
        $detailTransaksi = $this->ambilDetailTransaksi($request, $permintaanIds);

        // ===== RIWAYAT BARANG MASUK (dari "Tambah Stok" di Kelola ATK) =====
        $totalBarangMasuk = $this->queryRiwayatMasuk($request, $dariObj, $sampaiObj)->sum('jumlah');

        $riwayatMasuk = $this->queryRiwayatMasuk($request, $dariObj, $sampaiObj)
            ->latest('created_at')
            ->paginate(10, ['*'], 'page_masuk')
            ->withQueryString();

        return view('admin.laporan', array_merge($statCards, [
            'dari' => $dari,
            'sampai' => $sampai,
            'trenBulanan' => $trenBulanan,
            'daftarUnitKerja' => $daftarUnitKerja,
            'detailTransaksi' => $detailTransaksi,
            'riwayatMasuk' => $riwayatMasuk,
            'totalBarangMasuk' => $totalBarangMasuk,
        ]));
    }

    public function export(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);

        $daftarPermintaan = $this->queryPermintaan($request, $dariObj, $sampaiObj)
            ->latest('tanggal')
            ->get();

        $namaFile = 'laporan-permintaan-' . $dariObj->format('Ymd') . '-' . $sampaiObj->format('Ymd') . '.xlsx';

        return Excel::download(new LaporanPermintaanExport($daftarPermintaan), $namaFile);
    }

    public function exportDetail(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);

        $daftarPermintaan = $this->queryPermintaan($request, $dariObj, $sampaiObj)
            ->latest('tanggal')
            ->get();

        $namaFile = 'laporan-barang-per-permintaan-' . $dariObj->format('Ymd') . '-' . $sampaiObj->format('Ymd') . '.xlsx';

        return Excel::download(new LaporanPermintaanDetailExport($daftarPermintaan, $dariObj, $sampaiObj), $namaFile);
    }

    public function exportBarangMasuk(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);

        $daftarRiwayat = $this->queryRiwayatMasuk($request, $dariObj, $sampaiObj)
            ->latest('created_at')
            ->get();

        $namaFile = 'laporan-barang-masuk-' . $dariObj->format('Ymd') . '-' . $sampaiObj->format('Ymd') . '.xlsx';

        return Excel::download(new LaporanBarangMasukExport($daftarRiwayat, $dariObj, $sampaiObj), $namaFile);
    }

    /**
     * Export PDF sungguhan (bukan window.print()) untuk laporan detail transaksi.
     * Strukturnya disamakan dengan exportDetail(): 1 baris = 1 barang, ada
     * kolom No, Unit Kerja, dan baris TOTAL Jumlah/Disetujui di bagian bawah.
     */
    public function exportPdf(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);

        $daftarPermintaan = $this->queryPermintaan($request, $dariObj, $sampaiObj)
            ->latest('tanggal')
            ->get();

        $baris = collect();
        $nomor = 1;
        $totalJumlah = 0;
        $totalDisetujui = 0;

        foreach ($daftarPermintaan as $permintaan) {
            foreach ($permintaan->items as $item) {
                $disetujui = in_array($permintaan->status, ['Draft', 'Menunggu'])
                    ? '-'
                    : ($permintaan->status === 'Ditolak' ? 0 : ($item->jumlah_disetujui ?? $item->jumlah));

                $totalJumlah += (int) $item->jumlah;
                $totalDisetujui += is_numeric($disetujui) ? (int) $disetujui : 0;

                $baris->push([
                    'no' => $nomor++,
                    'nomor_permintaan' => $permintaan->nomor_permintaan,
                    'pemohon' => $permintaan->user->name ?? '-',
                    'nip' => $permintaan->user->nip ?? '-',
                    'unit_kerja' => $permintaan->user->unit_kerja ?? '-',
                    'tanggal' => $permintaan->tanggal->format('d-m-Y'),
                    'nama_barang' => $item->nama_barang,
                    'jumlah' => $item->jumlah,
                    'disetujui' => $disetujui,
                    'satuan' => $item->satuan,
                    'status' => $permintaan->status,
                    'catatan' => $this->bersihkanCatatanPdf($permintaan->catatan),
                ]);
            }
        }

        $namaFile = 'laporan-barang-per-permintaan-' . $dariObj->format('Ymd') . '-' . $sampaiObj->format('Ymd') . '.pdf';

        $pdf = Pdf::loadView('admin.laporan-pdf', [
            'baris' => $baris,
            'dari' => $dariObj,
            'sampai' => $sampaiObj,
            'totalJumlah' => $totalJumlah,
            'totalDisetujui' => $totalDisetujui,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

    public function exportBarangMasukPdf(Request $request)
    {
        [$dariObj, $sampaiObj] = $this->getRentangTanggal($request);

        $daftarRiwayat = $this->queryRiwayatMasuk($request, $dariObj, $sampaiObj)
            ->latest('created_at')
            ->get();

        $baris = collect();
        $nomor = 1;
        $totalJumlah = 0;

        foreach ($daftarRiwayat as $riwayat) {
            $totalJumlah += (int) $riwayat->jumlah;

            $baris->push([
                'no' => $nomor++,
                'tanggal' => $riwayat->created_at->format('d-m-Y H:i'),
                'nama_barang' => $riwayat->barang->nama_barang ?? '-',
                'kategori' => $riwayat->barang->kategori ?? '-',
                'jumlah' => $riwayat->jumlah,
                'satuan' => $riwayat->barang->satuan ?? '-',
                'keterangan' => $riwayat->keterangan ?: '-',
                'ditambahkan_oleh' => $riwayat->user->name ?? '-',
            ]);
        }

        $namaFile = 'laporan-barang-masuk-' . $dariObj->format('Ymd') . '-' . $sampaiObj->format('Ymd') . '.pdf';

        $pdf = Pdf::loadView('admin.laporan-barang-masuk-pdf', [
            'baris' => $baris,
            'dari' => $dariObj,
            'sampai' => $sampaiObj,
            'totalJumlah' => $totalJumlah,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

    /**
     * Buang bagian "[Alasan ditolak]" dan rapikan newline dari catatan,
     * supaya tidak berantakan saat dicetak di PDF.
     */
    private function bersihkanCatatanPdf(?string $catatan): string
    {
        $catatan = $catatan ?? '';

        if (str_contains($catatan, '[Alasan ditolak]')) {
            $catatan = explode('[Alasan ditolak]', $catatan)[0];
        }

        $catatan = trim(str_replace(["\r\n", "\r", "\n"], ' ', $catatan));

        return $catatan ?: '-';
    }
}