<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Katalog ATK - Sistem Permintaan ATK</title>
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
                <h1 class="text-base font-semibold text-gray-800">Daftar Katalog ATK</h1>
                <div class="flex items-center gap-4">
                    @include('partials.keranjang-badge')
                    @include('partials.notifikasi-dropdown')
                    @include('partials.avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-3">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- FILTER BAR --}}
                <form method="GET" action="{{ route('aatk.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-[220px]">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="cari"
                            value="{{ request('cari') }}"
                            placeholder="Ketik nama atau kode barang..."
                            class="w-full text-sm border border-gray-200 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 whitespace-nowrap">Kategori</span>
                        <select name="kategori" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ request('kategori') == '' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="Alat Tulis Kantor" {{ request('kategori') == 'Alat Tulis Kantor' ? 'selected' : '' }}>Alat Tulis Kantor</option>
                            <option value="Kertas & Buku" {{ request('kategori') == 'Kertas & Buku' ? 'selected' : '' }}>Kertas & Buku</option>
                            <option value="Tinta & Toner" {{ request('kategori') == 'Tinta & Toner' ? 'selected' : '' }}>Tinta & Toner</option>
                            <option value="Perlengkapan Meja" {{ request('kategori') == 'Perlengkapan Meja' ? 'selected' : '' }}>Perlengkapan Meja</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 whitespace-nowrap">Status Ketersediaan</span>
                        <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Menipis" {{ request('status') == 'Menipis' ? 'selected' : '' }}>Menipis</option>
                            <option value="Habis" {{ request('status') == 'Habis' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2.5 rounded-lg whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>

                    @if (request('cari') || request('kategori') || request('status'))
                        <a href="{{ route('aatk.index') }}" class="text-xs font-semibold text-gray-500 hover:text-red-600 whitespace-nowrap">
                            Reset Filter
                        </a>
                    @endif
                </form>

                {{-- GRID KATALOG --}}
                @php
                    $statusBadge = [
                        'Tersedia' => 'bg-green-500 text-white',
                        'Menipis' => 'bg-red-500 text-white',
                        'Habis' => 'bg-gray-400 text-white',
                    ];
                    $stokColor = [
                        'Tersedia' => 'text-blue-700',
                        'Menipis' => 'text-red-600',
                        'Habis' => 'text-gray-400',
                    ];

                    $katalog = $aatk ?? collect();
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @forelse ($katalog as $item)
                        @php
                            /** @var \App\Models\BarangAatk $item */
                            $status = $item->status;
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                            {{-- Kode barang --}}
                            <div class="px-4 pt-4">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                    {{ $item->kode_aatk }}
                                </span>
                            </div>

                            {{-- Konten --}}
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-gray-800 leading-snug">{{ $item->nama_barang }}</h3>
                                    <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full whitespace-nowrap {{ $statusBadge[$status] ?? 'bg-gray-300 text-white' }}">
                                        {{ $status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">{{ $item->kategori }}</p>

                                <div class="mt-auto flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-gray-400">Stok Tersedia</p>
                                        <p class="text-sm font-bold {{ $stokColor[$status] ?? 'text-gray-600' }}">
                                            {{ $item->stok }} <span class="font-normal text-xs">{{ $item->satuan }}</span>
                                        </p>
                                    </div>

                                    @if ($status === 'Habis')
                                        <button type="button" disabled class="flex items-center gap-1.5 bg-gray-100 text-gray-400 text-xs font-semibold px-3 py-2 rounded-lg cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            Habis
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('keranjang.tambah', $item->id) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Keranjang
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-xl border border-gray-100 shadow-sm p-10 text-center text-sm text-gray-400">
                            Tidak ada barang yang cocok dengan pencarian Anda.
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                @if (method_exists($katalog, 'links'))
                    <div class="pt-2">
                        {{ $katalog->links() }}
                    </div>
                @endif
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