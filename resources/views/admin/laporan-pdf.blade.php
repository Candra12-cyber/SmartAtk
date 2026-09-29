<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Permintaan ATK/Persediaan</title>
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

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-menunggu, .badge-diproses { background-color: #fffbeb; color: #b45309; }
        .badge-disetujui, .badge-selesai { background-color: #ecfdf5; color: #15803d; }
        .badge-dikirim { background-color: #eef2ff; color: #4338ca; }
        .badge-ditolak { background-color: #fef2f2; color: #b91c1c; }
        .badge-draft { background-color: #f3f4f6; color: #4b5563; }

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

    <p class="judul">Laporan Permintaan ATK/Persediaan</p>
    <p class="subjudul">
        {{ $dari->translatedFormat('d F Y') }} &ndash; {{ $sampai->translatedFormat('d F Y') }}
    </p>
    <p class="instansi">Pengadilan Negeri Garut</p>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Nomor Permintaan</th>
                <th style="width: 10%;">Pemohon</th>
                <th style="width: 9%;">NIP</th>
                <th style="width: 10%;">Unit Kerja</th>
                <th style="width: 6%;">Tanggal</th>
                <th style="width: 12%;">Nama Barang</th>
                <th style="width: 5%;">Jumlah</th>
                <th style="width: 6%;">Disetujui</th>
                <th style="width: 6%;">Satuan</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 17%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $row)
                @php
                    $kelasBadge = [
                        'Menunggu' => 'badge-menunggu',
                        'Disetujui' => 'badge-disetujui',
                        'Diproses' => 'badge-diproses',
                        'Dikirim' => 'badge-dikirim',
                        'Selesai' => 'badge-selesai',
                        'Ditolak' => 'badge-ditolak',
                        'Draft' => 'badge-draft',
                    ][$row['status']] ?? 'badge-draft';
                @endphp
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ $row['nomor_permintaan'] }}</td>
                    <td>{{ $row['pemohon'] }}</td>
                    <td>{{ $row['nip'] }}</td>
                    <td>{{ $row['unit_kerja'] }}</td>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td class="text-center">{{ $row['jumlah'] }}</td>
                    <td class="text-center">{{ $row['disetujui'] }}</td>
                    <td class="text-center">{{ $row['satuan'] }}</td>
                    <td class="text-center"><span class="badge {{ $kelasBadge }}">{{ $row['status'] }}</span></td>
                    <td>{{ $row['catatan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 16px; color: #94a3b8;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($baris->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="7" class="text-right">TOTAL (Jumlah Diminta / Disetujui)</td>
                    <td class="text-center">{{ $totalJumlah }}</td>
                    <td class="text-center">{{ $totalDisetujui }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer-note">Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; Sistem Permintaan ATK</p>

</body>
</html>