<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\Permintaan> $permintaan
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Saya - Sistem Permintaan AATK</title>
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
                    <span>Sistem Permintaan AATK</span>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">Permintaan Saya</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.notifikasi-dropdown')
                    @include('partials.avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6 space-y-6">

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Riwayat Permintaan</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau status permintaan Alat Tulis Kantor Anda.</p>
                    </div>
                    <a href="{{ route('permintaan.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Permintaan Baru
                    </a>
                </div>

                {{-- FILTER BAR --}}
                <form method="GET" action="{{ route('permintaan.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 flex-wrap">
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
                            placeholder="Cari nomor permintaan atau keperluan..."
                            class="w-full text-sm border border-gray-200 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 whitespace-nowrap">Filter Status:</span>
                        <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="Diverifikasi" {{ request('status') == 'Diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2.5 rounded-lg whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>

                    @if (request('cari') || request('status'))
                        <a href="{{ route('permintaan.index') }}" class="text-xs font-semibold text-gray-500 hover:text-red-600 whitespace-nowrap">
                            Reset Filter
                        </a>
                    @endif
                </form>

                {{-- TABLE --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Nomor Permintaan</th>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Jml Item</th>
                                    <th class="px-5 py-3 font-medium">Keperluan</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium">Tgl Update</th>
                                    <th class="px-5 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusColor = [
                                        'Menunggu' => 'bg-amber-50 text-amber-600',
                                        'Diverifikasi' => 'bg-sky-50 text-sky-600',
                                        'Disetujui' => 'bg-green-50 text-green-600',
                                        'Diproses' => 'bg-blue-50 text-blue-600',
                                        'Dikirim' => 'bg-indigo-50 text-indigo-600',
                                        'Selesai' => 'bg-gray-100 text-gray-600',
                                        'Ditolak' => 'bg-red-50 text-red-500',
                                    ];

                                    $daftar = $permintaan;
                                @endphp

                                @forelse ($daftar as $item)
                                    @php
                                        $nomor = $item->nomor_permintaan;
                                        $tanggal = $item->tanggal->format('d M Y');
                                        $jml = $item->items->count();
                                        $keperluan = \Illuminate\Support\Str::limit($item->catatan, 32);
                                        $status = $item->status;
                                        $update = $item->updated_at->format('d M Y');
                                        $id = $item->id;
                                        $warna = $statusColor[$status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 font-semibold text-blue-700">{{ $nomor }}</td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $tanggal }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $jml }}</td>
                                        <td class="px-5 py-3 text-gray-600 max-w-[220px] truncate">{{ $keperluan }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap {{ $warna }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $update }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <a href="{{ $id ? route('permintaan.show', $id) : '#' }}" class="inline-flex text-blue-500 hover:text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                            Belum ada permintaan yang diajukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 flex-wrap gap-3">
                        <p class="text-xs text-gray-400">
                            @if ($permintaan->total() > 0)
                                Menampilkan {{ $permintaan->firstItem() }}-{{ $permintaan->lastItem() }} dari {{ $permintaan->total() }} data
                            @else
                                Tidak ada data
                            @endif
                        </p>

                        <div>{{ $permintaan->links() }}</div>
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