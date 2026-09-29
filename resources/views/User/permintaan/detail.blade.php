<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Permintaan - Sistem Permintaan ATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">
        @include('partials.user-sidebar')

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between">
                <nav class="text-xs text-gray-500 flex items-center gap-1.5">
                    <a href="{{ route('permintaan.index') }}" class="hover:text-blue-600">Permintaan Saya</a>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">Detail Permintaan</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.notifikasi-dropdown')
                    @include('partials.avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                @php
                    /** @var \App\Models\Permintaan $permintaan */

                    // Data asli dari controller
                    $nomor = $permintaan->nomor_permintaan ?? 'REQ-2023-10-045';
                    $tanggal = $permintaan->tanggal ? (is_string($permintaan->tanggal) ? $permintaan->tanggal : $permintaan->tanggal->format('d F Y')) : '-';
                    $waktu = $permintaan->created_at ? $permintaan->created_at->format('H:i \W\I\B') : '-';
                    $status = $permintaan->status ?? 'Menunggu';

                    $statusColor = [
                        'Draft' => 'bg-gray-100 text-gray-600',
                        'Menunggu' => 'bg-amber-50 text-amber-600',
                        'Diverifikasi' => 'bg-sky-50 text-sky-600',
                        'Disetujui' => 'bg-green-50 text-green-600',
                        'Diproses' => 'bg-blue-50 text-blue-600',
                        'Dikirim' => 'bg-indigo-50 text-indigo-600',
                        'Selesai' => 'bg-emerald-50 text-emerald-600',
                        'Ditolak' => 'bg-red-50 text-red-500',
                    ][$status] ?? 'bg-blue-50 text-blue-600';

                    // Hitung tahapan status perjalanan secara dinamis berdasarkan status riil di database
                    $levelMap = [
                        'Draft' => 0,
                        'Menunggu' => 0,
                        'Diverifikasi' => 1,
                        'Disetujui' => 1,
                        'Diproses' => 1,
                        'Dikirim' => 2,
                        'Selesai' => 3,
                        'Ditolak' => -1,
                    ];
                    $lvl = $levelMap[$status] ?? 0;

                    $tahapan = [
                        [
                            'label' => 'Pengajuan',
                            'waktu' => $permintaan->created_at ? $permintaan->created_at->format('d M, H:i') : null,
                            'selesai' => $lvl > 0,
                            'aktif' => $lvl === 0,
                        ],
                        [
                            'label' => 'Diproses',
                            'waktu' => $lvl === 1 ? 'Saat ini' : ($lvl > 1 ? 'Selesai' : null),
                            'selesai' => $lvl > 1,
                            'aktif' => $lvl === 1,
                        ],
                        [
                            'label' => 'Dikirim',
                            'waktu' => $lvl === 2 ? 'Saat ini' : ($lvl > 2 ? 'Selesai' : null),
                            'selesai' => $lvl > 2,
                            'aktif' => $lvl === 2,
                        ],
                        [
                            'label' => 'Selesai',
                            'waktu' => $lvl === 3 ? 'Selesai' : null,
                            'selesai' => $lvl >= 3,
                            'aktif' => $lvl === 3,
                        ],
                    ];

                    $tahapanIcon = [
                        'Pengajuan' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'Diproses' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                        'Dikirim' => 'M9 17h6l3-3.5V9a1 1 0 00-1-1h-4V6a1 1 0 00-1-1H4a1 1 0 00-1 1v9a1 1 0 001 1h2m9 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m-5 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0',
                        'Selesai' => 'M5 13l4 4L19 7',
                    ];

                    // Data pemohon
                    $pemohon = $permintaan->user ?? null;

                    // Daftar barang
                    $barang = $permintaan->items ?? [
                        ['no' => 1, 'kode' => 'ATK-001', 'nama' => 'Kertas HVS A4 80gr Sinar Dunia', 'jumlah' => 5, 'satuan' => 'Rim'],
                        ['no' => 2, 'kode' => 'ATK-015', 'nama' => 'Tinta Printer Epson 003 Black', 'jumlah' => 2, 'satuan' => 'Botol'],
                        ['no' => 3, 'kode' => 'ATK-042', 'nama' => 'Pulpen Joyko Hitam 0.5mm', 'jumlah' => 1, 'satuan' => 'Kotak'],
                        ['no' => 4, 'kode' => 'ATK-088', 'nama' => 'Map Snelhecter Biru Plastik', 'jumlah' => 20, 'satuan' => 'Pcs'],
                    ];
                @endphp

                {{-- HEADER CARD --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Permintaan #{{ $nomor }}</h2>
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-3">
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $tanggal }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $waktu }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $statusColor }}">
                            {{ $status }}
                        </span>
                    </div>
                </div>

                @if ($status === 'Ditolak')
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-bold text-red-800">Permintaan Ditolak</h4>
                            <p class="text-xs text-red-600 mt-0.5">{{ $permintaan->catatan ?: 'Permintaan ini tidak disetujui oleh admin.' }}</p>
                        </div>
                    </div>
                @endif

                {{-- STATUS PERJALANAN --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-6">Status Perjalanan Permintaan</h3>

                    <div class="flex items-start justify-between overflow-x-auto gap-2">
                        @foreach ($tahapan as $i => $step)
                            <div class="flex flex-col items-center text-center flex-1 min-w-[90px]">
                                <div class="flex items-center w-full">
                                    @if ($i > 0)
                                        <div class="flex-1 h-0.5 {{ $step['selesai'] || ($tahapan[$i-1]['selesai'] ?? false) ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                                        {{ $step['selesai'] ? 'bg-blue-600 text-white' : (($step['aktif'] ?? false) ? 'bg-blue-100 text-blue-600 ring-2 ring-blue-600' : 'bg-gray-100 text-gray-300') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tahapanIcon[$step['label']] }}" />
                                        </svg>
                                    </div>
                                    @if ($i < count($tahapan) - 1)
                                        <div class="flex-1 h-0.5 {{ $step['selesai'] ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                    @endif
                                </div>
                                <p class="text-xs font-semibold mt-2 {{ $step['selesai'] || ($step['aktif'] ?? false) ? 'text-gray-800' : 'text-gray-400' }}">
                                    {{ $step['label'] }}
                                </p>
                                <p class="text-[11px] {{ ($step['aktif'] ?? false) ? 'text-blue-600 font-medium' : 'text-gray-400' }} mt-0.5">
                                    {{ $step['waktu'] ?? '-' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- INFO PEMOHON + DAFTAR BARANG --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Informasi Pemohon --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Informasi Pemohon
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800">{{ $pemohon->name ?? 'Budi Santoso, S.H., M.H.' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">NIP</p>
                                <p class="font-semibold text-gray-700">{{ $pemohon->nip ?? '198507122010121004' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">Jabatan / Bagian</p>
                                <p class="font-semibold text-gray-700">{{ $pemohon->jabatan ?? 'Panitera Pengganti' }} / {{ $pemohon->unit_kerja ?? 'Kepaniteraan Pidana' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">Ruangan</p>
                                <p class="font-semibold text-gray-700">{{ $permintaan->user->ruangan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">Keperluan</p>
                                <p class="text-gray-600 bg-gray-50 rounded-lg p-3 mt-1 text-xs leading-relaxed">
                                    {{ $permintaan->catatan ?? '"Keperluan rutin persidangan dan pencetakan berkas perkara BAP selama bulan November."' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Barang Diminta --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Daftar Barang Diminta
                            </h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                                Total: {{ count($barang) }} Item
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                        <th class="px-5 py-3 font-medium">No</th>
                                        <th class="px-5 py-3 font-medium">Kode ATK</th>
                                        <th class="px-5 py-3 font-medium">Nama Barang</th>
                                        <th class="px-5 py-3 font-medium text-right">Jumlah</th>
                                        @if (! in_array($status, ['Draft', 'Menunggu', 'Ditolak']))
                                            <th class="px-5 py-3 font-medium text-right">Disetujui</th>
                                        @endif
                                        <th class="px-5 py-3 font-medium">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barang as $i => $item)
                                        @php
                                            $jumlahDisetujui = $item['jumlah_disetujui'] ?? $item->jumlah_disetujui ?? null;
                                        @endphp
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="px-5 py-3 text-gray-500">{{ $item['no'] ?? $i + 1 }}</td>
                                            <td class="px-5 py-3 text-gray-500">{{ $item['kode'] ?? $item->kode_aatk ?? '-' }}</td>
                                            <td class="px-5 py-3 font-medium text-gray-700">{{ $item['nama'] ?? $item->nama_barang ?? '-' }}</td>
                                            <td class="px-5 py-3 text-right text-gray-700">{{ $item['jumlah'] ?? $item->jumlah ?? '-' }}</td>
                                            @if (! in_array($status, ['Draft', 'Menunggu', 'Ditolak']))
                                                <td class="px-5 py-3 text-right font-medium {{ $jumlahDisetujui === 0 ? 'text-red-500' : 'text-green-600' }}">
                                                    {{ $jumlahDisetujui ?? '-' }}
                                                </td>
                                            @endif
                                            <td class="px-5 py-3 text-gray-500">{{ $item['satuan'] ?? $item->satuan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 flex items-center justify-between text-[11px] text-gray-400 bg-white">
                <span>&copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2</span>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-blue-600 underline">Panduan Pengguna</a>
                    <a href="#" class="hover:text-blue-600 underline">Hubungi IT</a>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>