<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang ATK</title>
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

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-tersedia { background-color: #ecfdf5; color: #15803d; }
        .badge-menipis  { background-color: #fffbeb; color: #b45309; }
        .badge-habis    { background-color: #fef2f2; color: #b91c1c; }

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

    <p class="judul">Data Barang ATK</p>
    <p class="subjudul">Dicetak per {{ now()->translatedFormat('d F Y') }}</p>
    <p class="instansi">Pengadilan Negeri Garut</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 13%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarBarang as $index => $item)
                @php
                    $kelasBadge = [
                        'Tersedia' => 'badge-tersedia',
                        'Menipis' => 'badge-menipis',
                        'Habis' => 'badge-habis',
                    ][$item->status] ?? 'badge-tersedia';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_aatk }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->stok }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center"><span class="badge {{ $kelasBadge }}">{{ $item->status }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16px; color: #94a3b8;">
                        Belum ada data barang ATK.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer-note">Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; Sistem Permintaan ATK</p>

</body>
</html>