<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Permintaan ATK</title>
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
                <h1 class="text-base font-semibold text-blue-900">Sistem Permintaan ATK</h1>
                <div class="flex items-center gap-4">
                    @include('partials.notifikasi-dropdown')
                    @include('partials.avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name ?? 'Ahmad' }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Berikut adalah ringkasan permintaan ATK Anda hari ini.</p>
                    </div>
                    <a href="{{ route('permintaan.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Permintaan ATK
                    </a>
                </div>

                {{-- STAT CARDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $stats = [
                            ['label' => 'Total Permintaan', 'value' => $totalPermintaan ?? 12, 'color' => 'blue', 'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2'],
                            ['label' => 'Menunggu Persetujuan', 'value' => $menunggu ?? 3, 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Disetujui', 'value' => $disetujui ?? 5, 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Selesai', 'value' => $selesai ?? 4, 'color' => 'sky', 'icon' => 'M5 13l4 4L19 7'],
                        ];
                        $colorMap = [
                            'blue' => 'bg-blue-50 text-blue-600',
                            'amber' => 'bg-amber-50 text-amber-600',
                            'green' => 'bg-green-50 text-green-600',
                            'sky' => 'bg-sky-50 text-sky-600',
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 mb-2">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $colorMap[$stat['color']] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TABLE + SIDE PANEL --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Permintaan Terbaru --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">Permintaan Terbaru</h3>
                            <a href="{{ route('permintaan.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                        <th class="px-5 py-3 font-medium">Nomor</th>
                                        <th class="px-5 py-3 font-medium">Tanggal</th>
                                        <th class="px-5 py-3 font-medium">Jumlah Item</th>
                                        <th class="px-5 py-3 font-medium">Status</th>
                                        <th class="px-5 py-3 font-medium text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $permintaan = $permintaanTerbaru ?? [
                                            ['nomor' => 'REQ-2023-10-001', 'tanggal' => '24 Okt 2023', 'jumlah' => '3 item', 'status' => 'Menunggu', 'warna' => 'bg-amber-50 text-amber-600'],
                                            ['nomor' => 'REQ-2023-10-002', 'tanggal' => '22 Okt 2023', 'jumlah' => '1 item', 'status' => 'Disetujui', 'warna' => 'bg-green-50 text-green-600'],
                                            ['nomor' => 'REQ-2023-10-003', 'tanggal' => '20 Okt 2023', 'jumlah' => '5 item', 'status' => 'Selesai', 'warna' => 'bg-sky-50 text-sky-600'],
                                        ];
                                        $warnaStatus = [
                                            'Draft' => 'bg-gray-100 text-gray-600',
                                            'Menunggu' => 'bg-amber-50 text-amber-600',
                                            'Disetujui' => 'bg-green-50 text-green-600',
                                            'Diproses' => 'bg-blue-50 text-blue-600',
                                            'Dikirim' => 'bg-indigo-50 text-indigo-600',
                                            'Selesai' => 'bg-sky-50 text-sky-600',
                                            'Ditolak' => 'bg-red-50 text-red-500',
                                        ];
                                    @endphp

                                    @php
                                        /** @var \App\Models\Permintaan $item */
                                    @endphp
                                    @foreach ($permintaan as $item)
                                        @php
                                            $isArray = is_array($item);
                                            $nomor = $isArray ? $item['nomor'] : $item->nomor_permintaan;
                                            $tanggal = $isArray ? $item['tanggal'] : $item->tanggal->format('d M Y');
                                            $jumlah = $isArray ? $item['jumlah'] : $item->items->count() . ' item';
                                            $status = $isArray ? $item['status'] : $item->status;
                                            $warna = $isArray ? $item['warna'] : ($warnaStatus[$status] ?? 'bg-gray-100 text-gray-600');
                                            $id = $isArray ? null : $item->id;
                                        @endphp
                                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                            <td class="px-5 py-3 font-medium text-gray-700">{{ $nomor }}</td>
                                            <td class="px-5 py-3 text-gray-500">{{ $tanggal }}</td>
                                            <td class="px-5 py-3 text-gray-500">{{ $jumlah }}</td>
                                            <td class="px-5 py-3">
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $warna }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <a href="{{ $id ? route('permintaan.show', $id) : '#' }}" class="inline-flex text-blue-500 hover:text-blue-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- AATK Tersedia --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-800">ATK Tersedia</h3>
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            @php
                                $aatk = $aatkTersedia ?? [
                                    ['nama' => 'Kertas HVS A4', 'kategori' => 'Alat Tulis', 'stok' => '50 Rim', 'status' => 'Tersedia', 'warna' => 'bg-green-50 text-green-600', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                    ['nama' => 'Pulpen Hitam', 'kategori' => 'Alat Tulis', 'stok' => '12 Box', 'status' => 'Menipis', 'warna' => 'bg-amber-50 text-amber-600', 'icon' => 'M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94l-3.11 1.243a.5.5 0 01-.65-.65l1.243-3.11a4 4 0 01.94-1.414z'],
                                    ['nama' => 'Map Snelhecter', 'kategori' => 'Perlengkapan Meja', 'stok' => '0 Pcs', 'status' => 'Habis', 'warna' => 'bg-red-50 text-red-500', 'icon' => 'M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z'],
                                ];
                            @endphp

                            @foreach ($aatk as $item)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $item['nama'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $item['kategori'] }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-semibold text-gray-600">{{ $item['stok'] }}</p>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $item['warna'] }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ route('aatk.index') }}" class="mt-5 block text-center text-xs font-semibold text-blue-600 border border-blue-100 rounded-lg py-2 hover:bg-blue-50">
                            Lihat Katalog ATK
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>