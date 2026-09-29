@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $permintaan */
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Permintaan - Sistem Permintaan ATK</title>
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
                <h1 class="text-base font-semibold text-blue-900">Kelola Permintaan</h1>
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
                    <h2 class="text-xl font-bold text-gray-800">Daftar Semua Permintaan</h2>
                    <p class="text-sm text-gray-500 mt-1">Tinjau, setujui, atau tolak permintaan ATK dari seluruh pegawai.</p>
                </div>

                {{-- FILTER BAR --}}
                <form method="GET" action="{{ route('admin.permintaan.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 flex-wrap">
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
                            placeholder="Cari nomor permintaan atau nama pegawai..."
                            class="w-full text-sm border border-gray-200 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                        >
                    </div>

                    <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2.5 rounded-lg whitespace-nowrap">
                        Cari
                    </button>

                    @if (request('cari') || request('status'))
                        <a href="{{ route('admin.permintaan.index') }}" class="text-xs font-semibold text-gray-500 hover:text-red-600 whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- TABLE --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Nomor</th>
                                    <th class="px-5 py-3 font-medium">Pemohon</th>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Jml Item</th>
                                    <th class="px-5 py-3 font-medium text-right">Disetujui</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusColor = [
                                        'Menunggu' => 'bg-amber-50 text-amber-600',
                                        'Disetujui' => 'bg-green-50 text-green-600',
                                        'Diproses' => 'bg-blue-50 text-blue-600',
                                        'Dikirim' => 'bg-indigo-50 text-indigo-600',
                                        'Selesai' => 'bg-gray-100 text-gray-600',
                                        'Ditolak' => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                @forelse (($permintaan ?? collect()) as $item)
                                    @php /** @var \App\Models\Permintaan $item */ @endphp
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3 font-semibold text-blue-700">{{ $item->nomor_permintaan }}</td>
                                        <td class="px-5 py-3 text-gray-700">{{ $item->user->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $item->tanggal->format('d M Y') }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->items->count() }}</td>
                                        <td class="px-5 py-3 text-right text-gray-500">
                                            @if (in_array($item->status, ['Draft', 'Menunggu']))
                                                -
                                            @elseif ($item->status === 'Ditolak')
                                                <span class="text-red-500">0</span>
                                            @else
                                                {{ $item->items->sum(fn ($i) => $i->jumlah_disetujui ?? $i->jumlah) }}/{{ $item->items->sum('jumlah') }}
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap {{ $statusColor[$item->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <a href="{{ route('admin.permintaan.show', $item->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                                Tinjau
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                                            Tidak ada data permintaan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (method_exists($permintaan, 'links'))
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $permintaan->links() }}
                        </div>
                    @endif
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