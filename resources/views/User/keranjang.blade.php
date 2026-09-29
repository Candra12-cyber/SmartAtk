<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang ATK - Sistem Permintaan ATK</title>
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
                    <a href="{{ route('aatk.index') }}" class="hover:text-blue-600">Daftar Katalog ATK</a>
                    <span>›</span>
                    <span class="text-gray-700 font-medium">Keranjang</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.keranjang-badge')
                    @include('partials.notifikasi-dropdown')
                    @include('partials.avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">Keranjang ATK</h2>
                    <p class="text-sm text-blue-600 mt-1">Periksa kembali barang yang akan diminta sebelum lanjut ke formulir permintaan.</p>
                </div>

                <div id="pesan-status" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-3"></div>
                <div id="pesan-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3"></div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    @if ($items->isEmpty())
                        <div class="p-10 text-center text-sm text-gray-400">
                            Keranjang masih kosong. Silakan pilih barang dari
                            <a href="{{ route('aatk.index') }}" class="text-blue-600 font-semibold hover:underline">Daftar Katalog ATK</a>.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                        <th class="py-3 px-4 font-medium">Nama ATK</th>
                                        <th class="py-3 px-4 font-medium">Stok Tersedia</th>
                                        <th class="py-3 px-4 font-medium">Jumlah Diminta</th>
                                        <th class="py-3 px-4 font-medium">Satuan</th>
                                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="border-b border-gray-50 last:border-0 baris-keranjang" data-item-id="{{ $item['id'] }}">
                                            <td class="py-3 px-4 font-semibold text-gray-700">{{ $item['nama'] }}</td>
                                            <td class="py-3 px-4 text-gray-500">{{ $item['stok'] }}</td>
                                            <td class="py-3 px-4">
                                                <input type="number" class="input-jumlah w-20 text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    value="{{ $item['jumlah'] }}" min="1" max="{{ $item['stok'] }}">
                                            </td>
                                            <td class="py-3 px-4 text-gray-500">{{ $item['satuan'] }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <button type="button" class="btn-hapus text-red-500 hover:text-red-700" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach         
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if ($items->isNotEmpty())
                    <div class="flex items-center justify-between">
                        <form method="POST" action="{{ route('keranjang.kosongkan') }}"
                            onsubmit="return confirm('Kosongkan seluruh keranjang?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-gray-500 hover:text-red-600">
                                Kosongkan Keranjang
                            </button>
                        </form>

                        <a href="{{ route('permintaan.create') }}"
                            class="flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                            Lanjut Buat Permintaan
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function tampilkanPesan(teks, error = false) {
            const kotak = document.getElementById(error ? 'pesan-error' : 'pesan-status');
            const lawan = document.getElementById(error ? 'pesan-status' : 'pesan-error');
            lawan.classList.add('hidden');
            kotak.textContent = teks;
            kotak.classList.remove('hidden');
            setTimeout(() => kotak.classList.add('hidden'), 3000);
        }

        function perbaruiBadgeKeranjang() {
            const jumlahBarisTersisa = document.querySelectorAll('.baris-keranjang').length;

            document.querySelectorAll('[data-keranjang-badge]').forEach(badge => {
                if (jumlahBarisTersisa > 0) {
                    badge.textContent = jumlahBarisTersisa > 9 ? '9+' : jumlahBarisTersisa;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        }

        // Update jumlah (tanpa reload)
        document.querySelectorAll('.input-jumlah').forEach(input => {
            input.addEventListener('change', async () => {
                const baris = input.closest('.baris-keranjang');
                const id = baris.dataset.itemId;

                try {
                    const res = await fetch(`/keranjang/${id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ jumlah: input.value }),
                    });

                    if (!res.ok) throw new Error();
                        perbaruiBadgeKeranjang();
                } catch (e) {
                    
                }
            });
        });

        // Hapus barang (tanpa reload)
        document.querySelectorAll('.btn-hapus').forEach(tombol => {
            tombol.addEventListener('click', async () => {
                const baris = tombol.closest('.baris-keranjang');
                const id = baris.dataset.itemId;

                if (!confirm('Hapus barang ini dari keranjang?')) return;

                try {
                    const res = await fetch(`/keranjang/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    if (!res.ok) throw new Error();
                    baris.remove();
                    tampilkanPesan('Barang dihapus dari keranjang.');
                    perbaruiBadgeKeranjang();

                    if (document.querySelectorAll('.baris-keranjang').length === 0) {
                        location.reload(); // biar tampilan "keranjang kosong" muncul
                    }
                } catch (e) {
                    tampilkanPesan('Gagal menghapus barang.', true);
                }
            });
        });
    </script>

</body>
</html>