<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Permintaan ATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            aside { display: none !important; }
            main { padding: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">

        @include('partials.admin-sidebar')

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between no">
                <h1 class="text-base font-semibold text-blue-900">Laporan</h1>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                @if (session('status'))
                    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3 no-print">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- HEADER + EXPORT --}}
                <div class="flex items-start justify-between flex-wrap gap-3 no-print">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Laporan Administrasi</h2>
                        <p class="text-sm text-gray-500 mt-1">Ringkasan operasional dan distribusi ATK periode berjalan.</p>
                    </div>
                    <div>
                        <div data-tab-content="keluar" class="flex items-center gap-2">
                            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Export PDF
                            </a>
                            <a href="{{ route('admin.laporan.export-detail', request()->query()) }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                </svg>
                                Export Excel
                            </a>
                        </div>
                        <div data-tab-content="masuk" class="flex items-center gap-2" style="display:none">
                            <a href="{{ route('admin.laporan.export-barang-masuk-pdf', request()->query()) }}" class="flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Export PDF
                            </a>
                            <a href="{{ route('admin.laporan.export-barang-masuk', request()->query()) }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                </svg>
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                {{-- TAB SWITCH: Barang Keluar / Barang Masuk --}}
                <div class="flex items-center gap-1 border-b border-gray-200 no-print">
                    <button type="button" data-tab-btn="keluar" class="tab-btn-laporan px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px border-transparent text-gray-400 hover:text-gray-600">
                        Barang Keluar (Permintaan)
                    </button>
                    <button type="button" data-tab-btn="masuk" class="tab-btn-laporan px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px border-transparent text-gray-400 hover:text-gray-600">
                        Barang Masuk (Tambah Stok)
                    </button>
                </div>

                {{-- STAT CARDS --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $statCards = [
                            [
                                'label' => 'Total Permintaan Periode Ini',
                                'value' => number_format($totalPermintaan ?? 0),
                                'delta' => $deltaPermintaan ?? null,
                                'color' => 'blue',
                            ],
                            [
                                'label' => 'Barang Dikeluarkan',
                                'value' => number_format($totalBarangKeluar ?? 0),
                                'delta' => $deltaBarangKeluar ?? null,
                                'color' => 'blue',
                            ],
                            [
                                'label' => 'Menunggu Verifikasi',
                                'value' => ($totalMenunggu ?? 0) . ' Unit Pengajuan',
                                'delta' => null,
                                'color' => 'amber',
                            ],
                            [
                                'label' => 'Stok Kritis',
                                'value' => ($totalStokKritis ?? 0) . ' Item Perlu Restok',
                                'delta' => null,
                                'color' => 'red',
                            ],
                        ];
                    @endphp
                    @foreach ($statCards as $card)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                            <p class="text-xs text-gray-500 mb-2">{{ $card['label'] }}</p>
                            <div class="flex items-end justify-between">
                                <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                                @if ($card['delta'] !== null)
                                    <span class="text-xs font-semibold {{ $card['delta'] >= 0 ? 'text-green-600' : 'text-red-500' }} mb-1">
                                        {{ $card['delta'] >= 0 ? '+' : '' }}{{ $card['delta'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- TREN PENGGUNAAN --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-bold text-gray-800">Tren Penggunaan ATK</h3>
                            <span class="text-xs font-medium text-gray-500 border border-gray-200 rounded-lg px-3 py-1.5">Bulanan</span>
                        </div>
                        <div class="flex items-end justify-between gap-3 h-40">
                            @php
                                $maxTren = collect($trenBulanan ?? [])->max('jumlah') ?: 1;
                            @endphp
                            @forelse (($trenBulanan ?? []) as $bulan)
                                @php $tinggi = $maxTren > 0 ? max(6, ($bulan['jumlah'] / $maxTren) * 100) : 6; @endphp
                                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2">
                                    <div class="w-full bg-blue-500 rounded-md" style="height: {{ $tinggi }}%" title="{{ $bulan['jumlah'] }} permintaan"></div>
                                    <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $bulan['label'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 w-full text-center self-center">Tidak ada data.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- FILTER LAPORAN --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 no-print">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">Filter Laporan</h3>
                        <form method="GET" action="{{ route('admin.laporan.index') }}" class="space-y-4">

                            <div>
                                <label class="block text-[11px] uppercase tracking-wide font-semibold text-gray-400 mb-1.5">Periode</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="date" name="dari" value="{{ $dari ?? '' }}"
                                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                    <input type="date" name="sampai" value="{{ $sampai ?? '' }}"
                                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] uppercase tracking-wide font-semibold text-gray-400 mb-1.5">Unit Kerja</label>
                                <select name="unit_kerja" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Unit</option>
                                    @foreach (($daftarUnitKerja ?? []) as $unit)
                                        <option value="{{ $unit }}" {{ request('unit_kerja') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] uppercase tracking-wide font-semibold text-gray-400 mb-2">Kategori Barang</label>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $kategoriList = ['Alat Tulis Kantor', 'Kertas & Buku', 'Tinta & Toner', 'Perlengkapan Meja'];
                                        $kategoriTerpilih = request('kategori');
                                    @endphp
                                    @foreach ($kategoriList as $kategori)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="kategori" value="{{ $kategori }}" class="hidden peer" {{ $kategoriTerpilih == $kategori ? 'checked' : '' }}>
                                            <span class="text-xs font-medium px-3 py-1.5 rounded-full border border-gray-200 text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 inline-block">
                                                {{ $kategori }}
                                            </span>
                                        </label>
                                    @endforeach
                                    @if ($kategoriTerpilih)
                                        <a href="{{ route('admin.laporan.index', array_merge(request()->except('kategori'))) }}" class="text-xs text-gray-400 hover:text-red-600 self-center">Hapus</a>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                Terapkan Filter
                            </button>
                            @if (request('dari') || request('sampai') || request('unit_kerja') || request('kategori'))
                                <a href="{{ route('admin.laporan.index') }}" class="block text-center text-xs font-semibold text-gray-500 hover:text-red-600">
                                    Reset Semua Filter
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- DETAIL TRANSAKSI (TAB: BARANG KELUAR) --}}
                <div data-tab-content="keluar" class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-wrap gap-3">
                        <h3 class="text-sm font-bold text-gray-800">Detail Transaksi Pengeluaran</h3>
                        <form method="GET" action="{{ route('admin.laporan.index') }}" class="relative no-print">
                            @foreach (request()->except(['cari', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ID atau Nama Barang..."
                                class="text-sm border border-gray-200 rounded-lg py-2 pl-9 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium w-12">No</th>
                                    <th class="px-5 py-3 font-medium">ID Transaksi</th>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Unit Kerja</th>
                                    <th class="px-5 py-3 font-medium">Nama Barang</th>
                                    <th class="px-5 py-3 font-medium text-right">Jumlah</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium text-center no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $badgeColor = [
                                        'Menunggu' => 'bg-amber-50 text-amber-600',
                                        'Disetujui' => 'bg-green-50 text-green-600',
                                        'Diproses' => 'bg-amber-50 text-amber-600',
                                        'Dikirim' => 'bg-indigo-50 text-indigo-600',
                                        'Selesai' => 'bg-green-50 text-green-600',
                                        'Ditolak' => 'bg-red-50 text-red-500',
                                        'Draft' => 'bg-gray-100 text-gray-500',
                                    ];
                                @endphp

                                @php
                                    /** @var \App\Models\PermintaanItem|object $row */
                                @endphp
                                @forelse (($detailTransaksi ?? []) as $row)
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 text-gray-400">{{ (method_exists($detailTransaksi, 'firstItem') ? $detailTransaksi->firstItem() : 1) + $loop->index }}</td>
                                        <td class="px-5 py-3 font-semibold text-blue-700 whitespace-nowrap">{{ $row->nomor_permintaan }}</td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $row->tanggal->format('d M Y') }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $row->user->unit_kerja ?? '-' }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-700">{{ $row->nama_barang }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700">{{ $row->jumlah }} {{ $row->satuan }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeColor[$row->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ $row->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-center no-print">
                                            <a href="{{ route('admin.permintaan.show', $row->permintaan_id) }}" class="inline-flex text-blue-500 hover:text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-10 text-center text-gray-400">Tidak ada transaksi pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (isset($detailTransaksi) && method_exists($detailTransaksi, 'links'))
                        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3 no-print">
                            <p class="text-xs text-gray-400">
                                Menampilkan {{ $detailTransaksi->firstItem() ?? 0 }}-{{ $detailTransaksi->lastItem() ?? 0 }} dari {{ $detailTransaksi->total() }} data
                            </p>
                            {{ $detailTransaksi->links() }}
                        </div>
                    @endif
                </div>

                {{-- RIWAYAT BARANG MASUK (TAB: BARANG MASUK) --}}
                <div data-tab-content="masuk" class="bg-white rounded-xl border border-gray-100 shadow-sm" style="display:none">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-wrap gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Riwayat Barang Masuk</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Histori penambahan stok lewat menu Kelola ATK &rarr; Tambah Stok. Total periode ini: <span class="font-semibold text-gray-600">{{ number_format($totalBarangMasuk ?? 0) }} item</span></p>
                        </div>
                        <form method="GET" action="{{ route('admin.laporan.index') }}" class="relative no-print">
                            @foreach (request()->except(['cari_masuk', 'page_masuk']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="cari_masuk" value="{{ request('cari_masuk') }}" placeholder="Cari nama barang..."
                                class="text-sm border border-gray-200 rounded-lg py-2 pl-9 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium w-12">No</th>
                                    <th class="px-5 py-3 font-medium">Tanggal &amp; Waktu</th>
                                    <th class="px-5 py-3 font-medium">Nama Barang</th>
                                    <th class="px-5 py-3 font-medium">Kategori</th>
                                    <th class="px-5 py-3 font-medium text-right">Jumlah Masuk</th>
                                    <th class="px-5 py-3 font-medium">Keterangan</th>
                                    <th class="px-5 py-3 font-medium">Ditambahkan Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($riwayatMasuk ?? []) as $row)
                                    @php /** @var \App\Models\RiwayatStok $row */ @endphp
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 text-gray-400">{{ (method_exists($riwayatMasuk, 'firstItem') ? $riwayatMasuk->firstItem() : 1) + $loop->index }}</td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $row->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-700">{{ $row->barang->nama_barang ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $row->barang->kategori ?? '-' }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-green-600">+{{ $row->jumlah }} {{ $row->barang->satuan ?? '' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $row->keterangan ?: '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $row->user->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">Tidak ada barang masuk pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (isset($riwayatMasuk) && method_exists($riwayatMasuk, 'links'))
                        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3 no-print">
                            <p class="text-xs text-gray-400">
                                Menampilkan {{ $riwayatMasuk->firstItem() ?? 0 }}-{{ $riwayatMasuk->lastItem() ?? 0 }} dari {{ $riwayatMasuk->total() }} data
                            </p>
                            {{ $riwayatMasuk->links() }}
                        </div>
                    @endif
                </div>

                <script>
                    (function () {
                        function gantiTabLaporan(tab) {
                            document.querySelectorAll('[data-tab-content]').forEach(function (el) {
                                el.style.display = (el.dataset.tabContent === tab) ? '' : 'none';
                            });
                            document.querySelectorAll('[data-tab-btn]').forEach(function (btn) {
                                var aktif = btn.dataset.tabBtn === tab;
                                btn.classList.toggle('border-blue-600', aktif);
                                btn.classList.toggle('text-blue-600', aktif);
                                btn.classList.toggle('border-transparent', !aktif);
                                btn.classList.toggle('text-gray-400', !aktif);
                            });
                            try {
                                var url = new URL(window.location.href);
                                url.searchParams.set('tab', tab);
                                window.history.replaceState({}, '', url);
                            } catch (e) {}
                        }

                        document.querySelectorAll('[data-tab-btn]').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                gantiTabLaporan(btn.dataset.tabBtn);
                            });
                        });

                        gantiTabLaporan(@json(request('tab', 'keluar')) === 'masuk' ? 'masuk' : 'keluar');
                    })();
                </script>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white no-print">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    </div>
</body>
</html>