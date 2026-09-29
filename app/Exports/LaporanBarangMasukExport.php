<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanBarangMasukExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $daftarRiwayat;
    protected ?Carbon $dari;
    protected ?Carbon $sampai;
    protected int $totalJumlah = 0;

    public function __construct($daftarRiwayat, ?Carbon $dari = null, ?Carbon $sampai = null)
    {
        $this->daftarRiwayat = $daftarRiwayat;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function collection()
    {
        $baris = collect();
        $nomor = 1;

        foreach ($this->daftarRiwayat as $riwayat) {
            $this->totalJumlah += (int) $riwayat->jumlah;

            $baris->push([
                $nomor++,
                $riwayat->created_at->format('d-m-Y H:i'),
                $riwayat->barang->nama_barang ?? '-',
                $riwayat->barang->kategori ?? '-',
                $riwayat->jumlah,
                $riwayat->barang->satuan ?? '-',
                $riwayat->keterangan ?: '-',
                $riwayat->user->name ?? '-',
            ]);
        }

        return $baris;
    }

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
            'No', 'Tanggal & Waktu', 'Nama Barang', 'Kategori',
            'Jumlah Masuk', 'Satuan', 'Keterangan', 'Ditambahkan Oleh',
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 18, 'C' => 28, 'D' => 20, 'E' => 14, 'F' => 12, 'G' => 30, 'H' => 20];
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
        $sheet->getStyle("A2:A{$lastRow}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
        $sheet->getStyle("E2:E{$lastRow}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $sheet->getHighestColumn();

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'Laporan Barang Masuk (Penambahan Stok ATK)');
                $sheet->setCellValue('A2', $this->teksPeriode());
                $sheet->setCellValue('A3', 'Pengadilan Negeri Garut');

                foreach ([1, 2, 3] as $baris) {
                    $sheet->mergeCells("A{$baris}:{$lastCol}{$baris}");
                    $sheet->getStyle("A{$baris}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => $baris === 1 ? 14 : 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $barisTotal = $sheet->getHighestRow() + 1;

                $sheet->mergeCells("A{$barisTotal}:D{$barisTotal}");
                $sheet->setCellValue("A{$barisTotal}", 'TOTAL Barang Masuk');
                $sheet->setCellValue("E{$barisTotal}", $this->totalJumlah);

                $sheet->getStyle("A{$barisTotal}:{$lastCol}{$barisTotal}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$barisTotal}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);
                $sheet->getStyle("E{$barisTotal}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
            },
        ];
    }
}