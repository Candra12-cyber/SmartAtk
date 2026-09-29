<?php

namespace App\Exports;

use App\Models\BarangAatk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StokBarangExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        $no = 0;

        return BarangAatk::orderBy('nama_barang')->get()->map(function ($item) use (&$no) {
            $no++;
            return [
                $no,
                $item->kode_aatk,
                $item->nama_barang,
                $item->kategori,
                $item->stok,
                $item->satuan,
                $item->status, // asumsi ada accessor/atribut status (Tersedia/Menipis/Habis)
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Barang', 'Kategori', 'Stok', 'Satuan', 'Status'];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 12, 'C' => 28, 'D' => 20, 'E' => 10, 'F' => 12, 'G' => 14];
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
        $sheet->getStyle("A2:B{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("E2:E{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}