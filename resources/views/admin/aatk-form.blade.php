<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($barang) ? 'Edit' : 'Tambah' }} Barang ATK - Sistem Permintaan ATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
        /* Penyesuaian tampilan TomSelect agar cocok dengan Tailwind */
        .ts-control {
            border-radius: 0.5rem !important;
            padding: 0.625rem 0.75rem !important;
            border-color: #e5e7eb !important;
            font-size: 0.875rem !important;
        }
        .ts-wrapper.focus .ts-control {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
            border-color: #3b82f6 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">

        @include('partials.admin-sidebar')

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between">
                <nav class="text-xs text-gray-500 flex items-center gap-1.5">
                    <a href="{{ route('admin.aatk.index') }}" class="hover:text-blue-600">Kelola ATK</a>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">{{ isset($barang) ? 'Edit Barang' : 'Tambah Barang' }}</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6">
                <div class="max-w-xl">
                    <h2 class="text-xl font-bold text-gray-800 mb-1">
                        {{ isset($barang) ? 'Edit Barang AATK' : 'Tambah Barang AATK Baru' }}
                    </h2>
                    <p class="text-sm text-gray-500 mb-6">
                        {{ isset($barang) ? 'Perbarui informasi barang di bawah ini.' : 'Lengkapi data barang yang akan ditambahkan ke katalog.' }}
                    </p>

                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($barang) ? route('admin.aatk.update', $barang->id) : route('admin.aatk.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        @csrf
                        @if (isset($barang))
                            @method('PUT')
                        @endif
                        
                        @if (isset($barang))
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode ATK</label>
                                <input type="text" value="{{ $barang->kode_aatk }}" disabled
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-400 cursor-not-allowed">
                                <p class="text-[11px] text-gray-400 mt-1">Kode barang tidak dapat diubah.</p>
                            </div>
                        @else
                            <div class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-blue-700">Kode ATK akan dibuat otomatis oleh sistem (contoh: <span class="font-semibold">{{ $kodeBerikutnya ?? 'ATK-001' }}</span>).</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Barang</label>
                            <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang ?? '') }}" required placeholder="Contoh: Kertas HVS A4 80gr"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                            <select id="select-kategori" name="kategori" required placeholder="Pilih atau ketik kategori...">
                                @php $kategoriTerpilih = old('kategori', $barang->kategori ?? ''); @endphp
                                <option value="">Pilih atau ketik kategori...</option>
                                @foreach (($daftarKategori ?? []) as $kategori)
                                    <option value="{{ $kategori }}" {{ strtolower(trim($kategoriTerpilih)) == strtolower(trim($kategori)) ? 'selected' : '' }}>{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Stok</label>
                                <input type="number" name="stok" min="0" value="{{ old('stok', $barang->stok ?? 0) }}" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan</label>
                                <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan ?? '') }}" required placeholder="Rim, Botol, Pcs, dll"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400 -mt-2">
                            Status ketersediaan (Tersedia/Menipis/Habis) dihitung otomatis dari jumlah stok.
                        </p>

                        <hr class="border-gray-100">

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.aatk.index') }}" class="border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                                {{ isset($barang) ? 'Simpan Perubahan' : 'Tambah Barang' }}
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    </div>

    <!-- Script JS TomSelect -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('#select-kategori', {
            create: true, // Mengizinkan pengguna mengetik kategori baru jika tidak ada di daftar
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
</body>
</html>