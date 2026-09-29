<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola ATK - Sistem Permintaan AATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">

        @include('partials.admin-sidebar')

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
           <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between">
                <h1 class="text-base font-semibold text-blue-900">Kelola ATK</h1>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                @if (session('status'))
                    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Data Barang ATK</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola daftar barang, stok, dan kategori Alat Tulis Kantor.</p>
                    </div>
                    <a href="{{ route('admin.aatk.export-pdf') }}" class="flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                        Export PDF
                    </a>
                    <a href="{{ route('admin.aatk.export-excel') }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        Export Excel
                    </a>
                    <a href="{{ route('admin.aatk.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Barang
                    </a>
                </div>

                {{-- FILTER BAR --}}
                <form method="GET" action="{{ route('admin.aatk.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-[220px]">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau kode barang..."
                            class="w-full text-sm border border-gray-200 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                    </div>
                    <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2.5 rounded-lg whitespace-nowrap">
                        Cari
                    </button>
                    @if (request('cari'))
                        <a href="{{ route('admin.aatk.index') }}" class="text-xs font-semibold text-gray-500 hover:text-red-600 whitespace-nowrap">Reset</a>
                    @endif
                </form>

                {{-- TABLE --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Kode</th>
                                    <th class="px-5 py-3 font-medium">Nama Barang</th>
                                    <th class="px-5 py-3 font-medium">Kategori</th>
                                    <th class="px-5 py-3 font-medium text-right">Stok</th>
                                    <th class="px-5 py-3 font-medium">Satuan</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Mendeteksi otomatis nama variabel dari Controller
                                    $listBarang = $barangs ?? $aatk ?? $aatks ?? $data ?? [];
                                    $statusBadge = [
                                        'Tersedia' => 'bg-green-50 text-green-600',
                                        'Menipis'  => 'bg-amber-50 text-amber-600',
                                        'Habis'    => 'bg-red-50 text-red-500',
                                    ];
                                @endphp

                                @php
                                    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\BarangAatk[] $barang */
                                    /** @var \App\Models\BarangAatk $item */
                                @endphp                     
                                @forelse ($listBarang as $item)
                                    @php
                                        // Menghitung status otomatis dari stok karena kolom 'status' tidak ada di DB
                                        $stok = $item->stok ?? 0;
                                        $status = $item->status ?? ($stok > 10 ? 'Tersedia' : ($stok > 0 ? 'Menipis' : 'Habis'));
                                    @endphp
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 font-medium text-blue-700">{{ $item->kode_aatk }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-700">{{ $item->nama_barang }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->kategori }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-700">{{ $item->stok }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->satuan }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusBadge[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-center gap-3">
                                                <button type="button" onclick="bukaModalStok({{ $item->id }}, '{{ $item->nama_barang }}')" class="text-green-500 hover:text-green-700" title="Tambah Stok">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                                <a href="{{ route('admin.aatk.edit', $item->id) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.aatk.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $item->nama_barang }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                                            Belum ada data barang ATK.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @php
                        $paginator = $barangs ?? $aatk ?? $aatks ?? $data ?? null;
                    @endphp

                    @php
                        $paginator = $aatk ?? collect();
                    @endphp
                    @if ($paginator && method_exists($paginator, 'links'))
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $paginator->links() }}
                        </div>
                    @endif
                </div>

                {{-- MODAL TAMBAH STOK --}}
                <div id="modal-stok" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center px-4">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6">
                        <h3 class="text-base font-bold text-gray-800 mb-1">Tambah Stok</h3>
                        <p class="text-sm text-gray-500 mb-4">Barang: <span id="modal-stok-nama" class="font-semibold text-gray-700"></span></p>
                        <form id="form-tambah-stok" method="POST" action="">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Masuk</label>
                                <input type="number" name="jumlah" min="1" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan (opsional)</label>
                                <input type="text" name="keterangan" placeholder="Misal: Pembelian dari toko ABC"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="tutupModalStok()" class="text-sm font-semibold text-gray-500 px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                    Batal
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function bukaModalStok(id, nama) {
                        document.getElementById('modal-stok-nama').textContent = nama;
                        document.getElementById('form-tambah-stok').action = `/admin/aatk/${id}/tambah-stok`;
                        document.getElementById('modal-stok').classList.remove('hidden');
                    }
                    function tutupModalStok() {
                        document.getElementById('modal-stok').classList.add('hidden');
                    }
                </script>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    </div>
</body>
</html>