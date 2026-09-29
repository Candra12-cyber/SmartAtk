@php
    /** @var \App\Models\Permintaan $permintaan */
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tinjau Permintaan - Sistem Permintaan ATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
        
        /* ATURAN KHUSUS PRINT UNTUK PAS 1 HALAMAN */
        @media print {
            @page {
                size: portrait;
                margin: 8mm; /* Margin kertas lebih tipis */
            }
            body { 
                background-color: #ffffff !important; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print, aside, header, footer { 
                display: none !important; 
            }
            main { 
                padding: 0 !important; 
                margin: 0 !important;
            }
            .print-border { 
                border: 1px solid #d1d5db !important; 
                box-shadow: none !important;
                border-radius: 6px !important;
            }
            /* Mencegah elemen terpotong ke halaman baru */
            .avoid-break {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">

        @include('partials.admin-sidebar')

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between no-print">
                <nav class="text-xs text-gray-500 flex items-center gap-1.5">
                    <a href="{{ route('admin.permintaan.index') }}" class="hover:text-blue-600">Kelola Permintaan</a>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">Tinjau Permintaan</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 print:p-0 space-y-6 print:space-y-3">

                @if (session('status'))
                    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3 no-print">
                        {{ session('status') }}
                    </div>
                @endif

                @php
                    $statusColor = [
                        'Menunggu' => 'bg-amber-50 text-amber-600',
                        'Disetujui' => 'bg-green-50 text-green-600',
                        'Diproses' => 'bg-blue-50 text-blue-600',
                        'Dikirim' => 'bg-indigo-50 text-indigo-600',
                        'Selesai' => 'bg-gray-100 text-gray-600',
                        'Ditolak' => 'bg-red-50 text-red-500',
                    ][$permintaan->status] ?? 'bg-gray-100 text-gray-600';
                @endphp

                {{-- HEADER CARD (HANYA DITAMPILKAN DI LAYAR) --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between flex-wrap gap-4 no-print">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Permintaan #{{ $permintaan->nomor_permintaan }}</h2>
                        <p class="text-xs text-gray-400 mt-1">Diajukan {{ $permintaan->tanggal->format('d F Y') }}</p>
                        @if ($permintaan->diprosesOleh)
                            <p class="text-xs text-gray-400 mt-0.5">
                                Diproses oleh <span class="font-semibold text-gray-600">{{ $permintaan->diprosesOleh->name }}</span>
                                &middot; {{ $permintaan->updated_at->format('d M Y, H:i') }}
                            </p>
                        @endif
                    </div>>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $statusColor }}">
                            {{ $permintaan->status }}
                        </span>
                        <button type="button" onclick="window.print()" class="no-print flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak
                        </button>
                    </div>
                </div>

                {{-- JUDUL CETAK --}}
                <div class="text-center mb-4 print:mb-2">
                    <h1 class="text-lg print:text-base font-bold text-gray-800 uppercase tracking-wide">
                        Formulir Permintaan ATK/Persediaan
                    </h1>
                    <p class="text-xs text-gray-500 print:text-[10px]">Pengadilan Negeri Garut</p>
                </div>

                {{-- ALASAN PENOLAKAN (JIKA DITOLAK) --}}
                @if ($permintaan->status === 'Ditolak' && $permintaan->alasan_penolakan)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 print:p-2 text-sm print:text-xs text-red-700 avoid-break">
                        <span class="font-bold block mb-1">Alasan Penolakan:</span>
                        <p>{{ $permintaan->alasan_penolakan }}</p>
                    </div>
                @endif

                {{-- GRID INFORMASI PEMOHON & DAFTAR BARANG --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print:gap-3">

                    {{-- INFORMASI PEMOHON --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 print:p-3 print-border avoid-break">
                        <h3 class="text-sm print:text-xs font-bold text-gray-800 mb-3 print:mb-2">Informasi Pemohon</h3>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-3 print:gap-y-1.5 text-sm print:text-xs">
                            <div>
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">Hari / Tanggal</p>
                                <p class="font-semibold text-gray-800">{{ $permintaan->tanggal->translatedFormat('l, d F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">NIP</p>
                                <p class="font-semibold text-gray-700">{{ $permintaan->user->nip ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800">{{ $permintaan->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">Jabatan / Unit Kerja</p>
                                <p class="font-semibold text-gray-700">{{ $permintaan->user->jabatan ?? '-' }} / {{ $permintaan->user->unit_kerja ?? '-' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">Ruangan</p>
                                <p class="font-semibold text-gray-700">{{ $permintaan->user->ruangan ?? '-' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[11px] print:text-[9px] uppercase tracking-wide text-gray-400">Keperluan</p>
                                <p class="text-gray-600 bg-gray-50 print:bg-transparent rounded-lg p-2 print:p-0 mt-0.5 text-xs print:text-[11px] leading-relaxed">
                                    {{ $permintaan->catatan ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- DAFTAR BARANG --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm print-border avoid-break">
                        <div class="flex items-center justify-between px-5 py-3 print:px-3 print:py-2 border-b border-gray-100">
                            <h3 class="text-sm print:text-xs font-bold text-gray-800">Daftar Barang Diminta</h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 no-print">
                                Total: {{ $permintaan->items->count() }} Item
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm print:text-xs">
                                <thead>
                                    <tr class="text-left text-xs print:text-[10px] text-gray-400 border-b border-gray-100">
                                        <th class="px-4 py-2 print:px-2 print:py-1 font-medium">No</th>
                                        <th class="px-4 py-2 print:px-2 print:py-1 font-medium">Nama Barang</th>
                                        <th class="px-4 py-2 print:px-2 print:py-1 font-medium text-right">Jumlah</th>
                                        @if ($permintaan->status !== 'Ditolak')
                                            <th class="px-4 py-2 print:px-2 print:py-1 font-medium text-right">Disetujui</th>
                                        @endif
                                        <th class="px-4 py-2 print:px-2 print:py-1 font-medium">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permintaan->items as $i => $item)
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="px-4 py-2 print:px-2 print:py-1 text-gray-500">{{ $i + 1 }}</td>
                                            <td class="px-4 py-2 print:px-2 print:py-1 font-medium text-gray-700">{{ $item->nama_barang }}</td>
                                            <td class="px-4 py-2 print:px-2 print:py-1 text-right text-gray-700">{{ $item->jumlah }}</td>
                                            @if ($permintaan->status === 'Menunggu')
                                                <td class="px-4 py-2 print:px-2 print:py-1 text-right">
                                                    <span class="hidden print:inline">{{ $item->jumlah_disetujui ?? $item->jumlah }}</span>
                                                    <input
                                                        type="number"
                                                        name="disetujui[{{ $item->id }}]"
                                                        form="form-setujui"
                                                        min="0"
                                                        max="{{ $item->jumlah }}"
                                                        value="{{ $item->jumlah }}"
                                                        class="w-16 text-xs text-right border border-gray-200 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 no-print"
                                                    >
                                                </td>
                                            @elseif ($permintaan->status !== 'Ditolak')
                                                <td class="px-4 py-2 print:px-2 print:py-1 text-right text-gray-700">
                                                    {{ $item->jumlah_disetujui ?? $item->jumlah }}
                                                </td>
                                            @endif
                                            <td class="px-4 py-2 print:px-2 print:py-1 text-gray-500">{{ $item->satuan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TANDA TANGAN (RINGKAS & TIDAK AKAN TERPOTONG) --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 print:p-3 print-border avoid-break">
                    <h3 class="text-sm print:text-xs font-bold text-gray-800 mb-2 print:mb-1">Tanda Tangan</h3>
                    <table class="w-full text-xs border border-gray-300 border-collapse">
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top w-1/2">
                                    <p class="text-[11px] print:text-[10px] text-gray-500">Pengaju</p>
                                    <p class="font-semibold text-gray-800 mt-0.5">{{ $permintaan->user->name ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1">NIP: {{ $permintaan->user->nip ?? '-' }}</p>
                                </td>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top w-1/2">
                                    <p class="text-[11px] print:text-[10px] text-gray-500 mb-8 print:mb-6">Tanda tangan</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top">
                                    <p class="text-[11px] print:text-[10px] text-gray-500">Kasubbag Umum dan Keuangan</p>
                                </td>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top">
                                    <p class="text-[11px] print:text-[10px] text-gray-500 mb-8 print:mb-6">Tanda tangan</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top">
                                    <p class="text-[11px] print:text-[10px] text-gray-500">Operator BMN</p>
                                </td>
                                <td class="border border-gray-300 px-3 py-2 print:py-1.5 align-top">
                                    <p class="text-[11px] print:text-[10px] text-gray-500 mb-8 print:mb-6">Tanda tangan</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- AKSI ADMIN --}}
                @if ($permintaan->status === 'Menunggu')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 no-print">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">Tindak Lanjut Permintaan</h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form id="form-setujui" method="POST" action="{{ route('admin.permintaan.setujui', $permintaan->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setujui Permintaan
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('modal-tolak').classList.remove('hidden')" class="flex-1 flex items-center justify-center gap-2 bg-white border border-red-300 text-red-600 hover:bg-red-50 text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak Permintaan
                            </button>
                        </div>
                    </div>
                @elseif ($permintaan->status === 'Disetujui')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 no-print">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">Update Status Pengiriman</h3>
                        <form method="POST" action="{{ route('admin.permintaan.update-status', $permintaan->id) }}" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="hidden" name="status" value="Diproses">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                Tandai Diproses
                            </button>
                        </form>
                    </div>
                @elseif ($permintaan->status === 'Diproses')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 no-print">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">Update Status Pengiriman</h3>
                        <form method="POST" action="{{ route('admin.permintaan.update-status', $permintaan->id) }}" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="hidden" name="status" value="Dikirim">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                Tandai Dikirim
                            </button>
                        </form>
                    </div>
                @elseif ($permintaan->status === 'Dikirim')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 no-print">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">Update Status Pengiriman</h3>
                        <form method="POST" action="{{ route('admin.permintaan.update-status', $permintaan->id) }}" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="hidden" name="status" value="Selesai">
                            <button type="submit" class="flex-1 bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                @endif
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white no-print">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    <div id="modal-tolak" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-1">Tolak Permintaan</h3>
            <p class="text-xs text-gray-500 mb-4">Berikan alasan penolakan agar pegawai memahami keputusan ini.</p>
            <form method="POST" action="{{ route('admin.permintaan.tolak', $permintaan->id) }}">
                @csrf
                <textarea name="alasan_penolakan" rows="3" required placeholder="Contoh: Stok tidak mencukupi, ajukan kembali bulan depan."
                    class="w-full text-sm border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"></textarea>
                <div class="flex items-center justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-tolak').classList.add('hidden')" class="text-sm font-semibold text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>