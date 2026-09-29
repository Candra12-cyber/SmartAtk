<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanPermintaanDetailExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    protected $daftarPermintaan;
    protected ?Carbon $dari;
    protected ?Carbon $sampai;

    /**
     * Total kumulatif kolom "Jumlah" dan "Disetujui", dihitung sambil
     * membangun collection() lalu dipakai untuk menulis baris TOTAL
     * di bagian bawah tabel Excel.
     */
    protected int $totalJumlah = 0;
    protected int $totalDisetujui = 0;

    public function __construct($daftarPermintaan, ?Carbon $dari = null, ?Carbon $sampai = null)
    {
        $this->daftarPermintaan = $daftarPermintaan;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    /**
     * Satu baris = satu barang yang diminta (bukan satu permintaan).
     */
    public function collection()
    {
        $baris = collect();
        $nomor = 1;

        foreach ($this->daftarPermintaan as $permintaan) {
            foreach ($permintaan->items as $item) {
                $disetujui = in_array($permintaan->status, ['Draft', 'Menunggu'])
                    ? '-'
                    : ($permintaan->status === 'Ditolak' ? 0 : ($item->jumlah_disetujui ?? $item->jumlah));

                $this->totalJumlah += (int) $item->jumlah;
                $this->totalDisetujui += is_numeric($disetujui) ? (int) $disetujui : 0;

                $baris->push([
                    $nomor++,
                    $permintaan->nomor_permintaan,
                    $permintaan->user->name ?? '-',
                    $permintaan->user->nip ? "'" . $permintaan->user->nip : '-',
                    $permintaan->user->unit_kerja ?? '-',
                    $permintaan->tanggal->format('d-m-Y'),
                    $item->nama_barang,
                    $item->jumlah,
                    $disetujui,
                    $item->satuan,
                    $permintaan->status,
                    $this->bersihkanCatatan($permintaan->catatan),
                    $this->ambilAlasanDitolak($permintaan->catatan),
                ]);
            }
        }

        return $baris;
    }

    protected function bersihkanCatatan(?string $catatan): string
    {
        $catatan = $catatan ?? '';

        if (str_contains($catatan, '[Alasan ditolak]')) {
            $catatan = explode('[Alasan ditolak]', $catatan)[0];
        }

        $catatan = trim(str_replace(["\r\n", "\r", "\n"], ' ', $catatan));

        return $catatan ?: '-';
    }

    /**
     * Ambil teks alasan penolakan saja dari catatan (yang disimpan setelah
     * tanda "[Alasan ditolak]:" oleh Admin\PermintaanController::tolak()).
     */
    protected function ambilAlasanDitolak(?string $catatan): string
    {
        $catatan = $catatan ?? '';

        if (! str_contains($catatan, '[Alasan ditolak]:')) {
            return '-';
        }

        $alasan = explode('[Alasan ditolak]:', $catatan)[1] ?? '';
        $alasan = trim(str_replace(["\r\n", "\r", "\n"], ' ', $alasan));

        return $alasan ?: '-';
    }

    /**
     * Teks periode untuk judul laporan, misal "September 2026"
     * atau "01 September 2026 - 15 Oktober 2026" kalau rentangnya lintas bulan.
     */
    protected function teksPeriode(): string
    {
        if (! $this->dari || ! $this->sampai) {
            return now()->translatedFormat('F Y');
        }

        $samaBulan = $this->dari->format('Y-m') === $this->sampai->format('Y-m');

        return $samaBulan
            ? $this->dari->translatedFormat('F Y')
            : $this->dari->translatedFormat('d F Y') . ' - ' . $this->sampai->translatedFormat('d F Y');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Permintaan',
            'Pemohon',
            'NIP',
            'Unit Kerja',
            'Tanggal',
            'Nama Barang',
            'Jumlah',
            'Disetujui',
            'Satuan',
            'Status',
            'Catatan',
            'Alasan Ditolak',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 22,
            'F' => 14,
            'G' => 25,
            'H' => 10,
            'I' => 10,
            'J' => 12,
            'K' => 14,
            'L' => 30,
            'M' => 30,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->getStyle("A2:A{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle("H2:I{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }

    /**
     * Sisipkan judul laporan di atas tabel (teks polos, tanpa border/tabel).
     * Baris 1: Formulir Permintaan ATK/Persediaan
     * Baris 2: nama bulan tahun (mengikuti periode filter)
     * Baris 3: Pengadilan Negeri Garut
     * Baris 4: kosong (jarak sebelum tabel)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $sheet->getHighestColumn(); // kolom terakhir tabel (M)

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'Laporan Permintaan ATK/Persediaan');
                $sheet->setCellValue('A2', $this->teksPeriode());
                $sheet->setCellValue('A3', 'Pengadilan Negeri Garut');

                foreach ([1, 2, 3] as $baris) {
                    $sheet->mergeCells("A{$baris}:{$lastCol}{$baris}");
                    $sheet->getStyle("A{$baris}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => $baris === 1 ? 14 : 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // ===== BARIS TOTAL, ditulis tepat di bawah baris data terakhir =====
                // Kolom: A=No, B=Nomor Permintaan, ... G=Nama Barang, H=Jumlah
                $barisTotal = $sheet->getHighestRow() + 1;

                $sheet->mergeCells("A{$barisTotal}:G{$barisTotal}");
                $sheet->setCellValue("A{$barisTotal}", 'TOTAL (Jumlah Diminta / Disetujui)');
                $sheet->setCellValue("H{$barisTotal}", $this->totalJumlah);
                $sheet->setCellValue("I{$barisTotal}", $this->totalDisetujui);

                $sheet->getStyle("A{$barisTotal}:{$lastCol}{$barisTotal}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$barisTotal}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getStyle("H{$barisTotal}:I{$barisTotal}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}