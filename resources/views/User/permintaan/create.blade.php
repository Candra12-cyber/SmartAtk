<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Permintaan - Sistem Permintaan ATK</title>
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
                    <a href="{{ route('user.dashboard') }}" class="hover:text-blue-600">Beranda</a>
                    <span>›</span>
                    <span class="text-gray-700 font-medium">Buat Permintaan</span>
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
                    <h2 class="text-xl font-bold text-gray-800">Formulir Permintaan ATK</h2>
                    <p class="text-sm text-blue-600 mt-1">Lengkapi formulir di bawah ini untuk mengajukan permintaan Alat Tulis dan Kebutuhan Kantor.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4">
                        <p class="font-semibold mb-1">Terjadi kesalahan pada formulir:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('permintaan.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {{-- KIRI: Daftar Barang + Catatan --}}
                        <div class="lg:col-span-2 space-y-6">

                            {{-- Daftar Barang --}}
                            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-bold text-gray-800">Daftar Barang</h3>
                                    <button type="button" id="btnTambahBarang"
                                        class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Barang
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm min-w-[420px]">
                                        <thead>
                                            <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                                <th class="py-2 pr-3 font-medium">Nama ATK</th>
                                                <th class="py-2 px-3 font-medium">Stok</th>
                                                <th class="py-2 px-3 font-medium">Diminta</th>
                                                <th class="py-2 px-3 font-medium">Satuan</th>
                                                <th class="py-2 pl-3 font-medium text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabelBarang">
                                            {{-- Baris awal akan ditambahkan lewat JS agar konsisten dengan baris baru --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">Catatan / Keperluan</h3>
                                <hr class="mb-4 border-gray-100">
                                <label class="block text-xs font-semibold text-blue-600 mb-2">Keterangan Tambahan (Opsional)</label>
                                <textarea
                                    name="catatan"
                                    rows="4"
                                    placeholder="Contoh: Kertas digunakan untuk pencetakan dokumen sidang minggu depan..."
                                    class="w-full text-sm border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                ></textarea>
                            </div>

                        </div>

                        {{-- KANAN: Informasi Pemohon --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 h-fit">
                            <h3 class="text-sm font-bold text-gray-800 mb-4">Informasi Pemohon</h3>
                            <div class="space-y-4 text-sm">
                                <div>
                                    <p class="text-xs text-gray-400">Tanggal Permintaan</p>
                                    <p class="font-semibold text-gray-700">{{ now()->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Nama Lengkap</p>
                                    <p class="font-semibold text-blue-700">{{ auth()->user()->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">NIP</p>
                                    <p class="font-semibold text-gray-700">{{ auth()->user()->nip ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Unit Kerja</p>
                                    <p class="font-semibold text-gray-700">{{ auth()->user()->unit_kerja ?? 'Kepaniteraan Pidana' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Jabatan</p>
                                    <p class="font-semibold text-gray-700">{{ auth()->user()->jabatan ?? 'Panitera Pengganti' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="submit" name="aksi" value="ajukan"
                            class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                            Ajukan Permintaan
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        // Daftar AATK diambil langsung dari database (dikirim dari controller/route)
        const daftarAATK = @json($daftarAATK ?? []);

        // Barang yang dipilih dari halaman "Daftar AATK" (lewat ?barang=NamaBarang) - cara lama
        const barangDipilih = @json(request('barang'));

        // Barang yang sudah dipilih lewat halaman Keranjang: [{nama, jumlah}, ...]
        const keranjang = @json($keranjang ?? []);

        let rowIndex = 0;

        function buatOptionsDatalist() {
            return daftarAATK.map(item =>
                `<option value="${item.nama.replace(/"/g, '&quot;')}"></option>`
            ).join('');
        }

        // Cari data barang berdasarkan nama yang diketik/dipilih (tidak case-sensitive)
        function cariBarang(nama) {
            const target = (nama || '').trim().toLowerCase();
            return daftarAATK.find(a => a.nama.toLowerCase() === target);
        }

        function tambahBaris(selectedNama = null, jumlahAwal = 1) {
            const item = daftarAATK.find(a => a.nama === selectedNama) || daftarAATK[0];
            if (!item) return; // tidak ada data barang sama sekali

            const stokMax = item.stok || jumlahAwal;
            const jumlah = Math.min(Math.max(parseInt(jumlahAwal) || 1, 1), stokMax || 1);

            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-50 last:border-0';
            tr.innerHTML = `
                <td class="py-2 pr-3">
                    <input type="text" list="daftar-aatk-${rowIndex}" autocomplete="off"
                        name="barang[${rowIndex}][nama]" value="${item.nama}"
                        placeholder="Ketik atau pilih nama AATK" title="${item.nama}"
                        class="w-full max-w-[140px] text-sm text-gray-800 border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 select-aatk truncate">
                    <datalist id="daftar-aatk-${rowIndex}">
                        ${buatOptionsDatalist()}
                    </datalist>
                </td>
                <td class="py-2 px-3 text-gray-500 stok-cell">${item.stok}</td>
                <td class="py-2 px-3">
                    <input type="number" min="1" max="${item.stok}" name="barang[${rowIndex}][jumlah]" value="${jumlah}"
                        class="w-20 text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </td>
                <td class="py-2 px-3 text-gray-500 satuan-cell">
                    ${item.satuan}
                    <input type="hidden" name="barang[${rowIndex}][satuan]" value="${item.satuan}" class="satuan-hidden-input">
                </td>
                <td class="py-2 pl-3 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 btn-hapus-baris">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;
            document.getElementById('tabelBarang').appendChild(tr);
            rowIndex++;
        }

        // Update stok & satuan otomatis saat nama AATK diketik/dipilih dan cocok persis dengan data
        document.getElementById('tabelBarang').addEventListener('input', function (e) {
            if (e.target.classList.contains('select-aatk')) {
                const row = e.target.closest('tr');
                const item = cariBarang(e.target.value);

                if (item) {
                    row.querySelector('.stok-cell').textContent = item.stok;

                    const satuanCell = row.querySelector('.satuan-cell');
                    satuanCell.childNodes[0].textContent = item.satuan;
                    row.querySelector('.satuan-hidden-input').value = item.satuan;

                    row.querySelector('input[type="number"]').max = item.stok;

                    e.target.title = item.nama;

                    e.target.classList.remove('border-red-400', 'text-red-600');
                    e.target.classList.add('border-gray-200', 'text-gray-800');
                } else {
                    // Nama yang diketik belum cocok dengan data AATK manapun
                    e.target.classList.remove('border-gray-200', 'text-gray-800');
                    e.target.classList.add('border-red-400', 'text-red-600');
                }
            }
        });

        // Hapus baris
        document.getElementById('tabelBarang').addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-hapus-baris');
            if (btn) {
                const rows = document.querySelectorAll('#tabelBarang tr');
                if (rows.length > 1) {
                    btn.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 baris barang.');
                }
            }
        });

        document.getElementById('btnTambahBarang').addEventListener('click', () => tambahBaris());

        // Cegah submit kalau ada nama AATK yang diketik tidak cocok dengan data yang tersedia
        document.querySelector('form').addEventListener('submit', function (e) {
            const inputs = document.querySelectorAll('#tabelBarang .select-aatk');
            let adaYangTidakValid = false;

            inputs.forEach(input => {
                if (!cariBarang(input.value)) {
                    adaYangTidakValid = true;
                    input.classList.remove('border-gray-200', 'text-gray-800');
                    input.classList.add('border-red-400', 'text-red-600');
                }
            });

            if (adaYangTidakValid) {
                e.preventDefault();
                alert('Ada nama AATK yang belum sesuai dengan daftar yang tersedia. Silakan pilih dari daftar yang muncul saat mengetik.');
            }
        });

        // Baris awal: prioritaskan isi keranjang (bisa lebih dari 1 barang)
        if (keranjang.length > 0) {
            keranjang.forEach(k => tambahBaris(k.nama, k.jumlah));
        } else if (barangDipilih) {
            // Cara lama: datang dari tombol tunggal (masih didukung untuk kompatibilitas)
            tambahBaris(barangDipilih);
        } else if (daftarAATK.length > 0) {
            tambahBaris();
        }
    </script>
</body>
</html>