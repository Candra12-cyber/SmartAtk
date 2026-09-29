<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanPermintaanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $daftarPermintaan;

    public function __construct($daftarPermintaan)
    {
        $this->daftarPermintaan = $daftarPermintaan;
    }

    /**
     * Pisahkan catatan asli dari alasan penolakan (kalau ada),
     * dan bersihkan newline supaya tidak "pecah" jadi baris baru di Excel.
     */
    protected function pisahkanCatatan(?string $catatan): array
    {
        $catatan = $catatan ?? '';

        if (str_contains($catatan, '[Alasan ditolak]')) {
            $bagian = explode('[Alasan ditolak]', $catatan, 2);

            $catatanUtama = trim($bagian[0]);
            $alasanDitolak = trim(str_replace(':', '', $bagian[1] ?? '', ));
            $alasanDitolak = ltrim($alasanDitolak, ": \t\n\r");
        } else {
            $catatanUtama = trim($catatan);
            $alasanDitolak = '';
        }

        // Hilangkan newline supaya tidak pecah jadi baris baru di sel Excel
        $bersihkan = fn ($teks) => trim(str_replace(["\r\n", "\r", "\n"], ' ', $teks));

        return [
            'catatan' => $bersihkan($catatanUtama) ?: '-',
            'alasan_ditolak' => $bersihkan($alasanDitolak) ?: '-',
        ];
    }

    public function collection()
    {
        return $this->daftarPermintaan->map(function ($item) {
            $catatan = $this->pisahkanCatatan($item->catatan);

            return [
                $item->nomor_permintaan,
                $item->user->name ?? '-',
                // Tambahkan tanda kutip satu di depan NIP agar Excel
                // memaksa membacanya sebagai teks, bukan angka.
                $item->user->nip ? "'" . $item->user->nip : '-',
                $item->tanggal->format('d-m-Y'),
                $item->items->count(),
                $item->status,
                $catatan['catatan'],
                $catatan['alasan_ditolak'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor Permintaan',
            'Pemohon',
            'NIP',
            'Tanggal',
            'Jumlah Item',
            'Status',
            'Catatan',
            'Alasan Ditolak',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 20,
            'D' => 14,
            'E' => 12,
            'F' => 14,
            'G' => 30,
            'H' => 30,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet->getStyle("E2:E{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}