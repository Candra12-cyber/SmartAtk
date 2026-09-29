<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; font-size: 10px; }

        .judul { text-align: center; font-size: 15px; font-weight: bold; margin: 0 0 2px 0; }
        .subjudul { text-align: center; font-size: 11px; margin: 0 0 2px 0; }
        .instansi { text-align: center; font-size: 11px; margin: 0 0 14px 0; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background-color: #eef2ff;
            color: #1e3a8a;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            text-align: left;
        }
        tbody td {
            border: 1px solid #e2e8f0;
            padding: 4px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) { background-color: #f8fafc; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        tfoot td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-weight: bold;
            background-color: #f1f5f9;
        }

        .footer-note {
            margin-top: 16px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <p class="judul">Laporan Barang Masuk (Penambahan Stok ATK)</p>
    <p class="subjudul">
        {{ $dari->translatedFormat('d F Y') }} &ndash; {{ $sampai->translatedFormat('d F Y') }}
    </p>
    <p class="instansi">Pengadilan Negeri Garut</p>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">Tanggal &amp; Waktu</th>
                <th style="width: 22%;">Nama Barang</th>
                <th style="width: 16%;">Kategori</th>
                <th style="width: 10%;">Jumlah Masuk</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 16%;">Keterangan</th>
                <th style="width: 10%;">Ditambahkan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $row)
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td>{{ $row['kategori'] }}</td>
                    <td class="text-center">{{ $row['jumlah'] }}</td>
                    <td class="text-center">{{ $row['satuan'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td>{{ $row['ditambahkan_oleh'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada barang masuk pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if (count($baris) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">TOTAL Barang Masuk</td>
                    <td class="text-center">{{ $totalJumlah }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer-note">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>

</body>
</html>