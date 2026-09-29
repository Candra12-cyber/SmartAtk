<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Permintaan ATK</title>
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
                <h1 class="text-base font-semibold text-blue-900">Dashboard Admin</h1>
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

                <div>
                    <h2 class="text-xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas Sistem Permintaan ATK.</p>
                </div>

                {{-- STAT CARDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $stats = [
                            ['label' => 'Menunggu Persetujuan', 'value' => $jumlahMenunggu ?? 0, 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'url' => route('admin.permintaan.index', ['status' => 'Menunggu'])],
                            ['label' => 'Total Pegawai', 'value' => $jumlahPegawai ?? 0, 'color' => 'blue', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3c0 1.657-3.134 3-7 3s-7-1.343-7-3', 'url' => route('admin.pegawai.index')],
                            ['label' => 'Barang Menipis/Habis', 'value' => $jumlahMenipis ?? 0, 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'url' => route('admin.aatk.index')],
                            ['label' => 'Selesai Bulan Ini', 'value' => $jumlahSelesai ?? 0, 'color' => 'green', 'icon' => 'M5 13l4 4L19 7', 'url' => route('admin.permintaan.index', ['status' => 'Selesai'])],
                        ];
                        $colorMap = [
                            'blue' => 'bg-blue-50 text-blue-600',
                            'amber' => 'bg-amber-50 text-amber-600',
                            'green' => 'bg-green-50 text-green-600',
                            'red' => 'bg-red-50 text-red-600',
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <a href="{{ $stat['url'] }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center justify-between hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-xs text-gray-500 mb-2">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $colorMap[$stat['color']] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- PERMINTAAN MENUNGGU PERSETUJUAN --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800">Permintaan Menunggu Persetujuan</h3>
                        <a href="{{ route('admin.permintaan.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Nomor</th>
                                    <th class="px-5 py-3 font-medium">Pemohon</th>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Jml Item</th>
                                    <th class="px-5 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    /** @var \App\Models\Permintaan $item */
                                @endphp
                                @forelse (($permintaanMenunggu ?? []) as $item)
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 font-semibold text-blue-700">
                                            {{ $item->nomor_permintaan }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-700">
                                            {{ $item->user?->name ?? '-' }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-500">
                                            {{ $item->created_at?->format('d M Y') ?? '-' }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-500">
                                            {{ $item->items?->count() ?? 0 }}
                                        </td>

                                        <td class="px-5 py-3 text-center">
                                            <a href="{{ route('admin.permintaan.show', $item->id) }}"
                                            class="text-xs font-semibold text-blue-600 hover:underline">
                                                Tinjau
                                            </a>
                                        </td>                                   
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">
                                            Tidak ada permintaan yang menunggu persetujuan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    </div>
</body>
</html>