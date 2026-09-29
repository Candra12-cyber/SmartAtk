<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai - Sistem Permintaan ATK</title>
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
                <h1 class="text-base font-semibold text-blue-900">Kelola Pegawai</h1>
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

                @if (session('password_baru'))
                    <div class="text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                        Password baru untuk <strong>{{ session('nama_pegawai') }}</strong>:
                        <span class="font-mono font-bold bg-white px-2 py-0.5 rounded border border-blue-200">{{ session('password_baru') }}</span>
                        — sampaikan ke pegawai secara langsung/tertulis, bukan lewat chat.
                    </div>
                @endif

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Data Pegawai</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola akun pegawai yang dapat mengakses Sistem Permintaan ATK.</p>
                    </div>
                    <a href="{{ route('admin.pegawai.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pegawai
                    </a>
                </div>

                {{-- FILTER BAR --}}
                <form method="GET" action="{{ route('admin.pegawai.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-[220px]">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, NIP, atau username..."
                            class="w-full text-sm border border-gray-200 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                    </div>
                    <select name="role" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ request('role') == '' ? 'selected' : '' }}>Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                    <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2.5 rounded-lg whitespace-nowrap">
                        Cari
                    </button>
                    @if (request('cari') || request('role'))
                        <a href="{{ route('admin.pegawai.index') }}" class="text-xs font-semibold text-gray-500 hover:text-red-600 whitespace-nowrap">Reset</a>
                    @endif
                </form>

                {{-- TABLE --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Nama</th>
                                    <th class="px-5 py-3 font-medium">NIP</th>
                                    <th class="px-5 py-3 font-medium">Username</th>
                                    <th class="px-5 py-3 font-medium">Unit Kerja</th>
                                    <th class="px-5 py-3 font-medium">Ruangan</th>
                                    <th class="px-5 py-3 font-medium">Role</th>
                                    <th class="px-5 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\User[] $pegawais */
                                    /** @var \App\Models\User $pegawai */
                                @endphp
                                @forelse (($pegawai ?? []) as $item)
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center text-xs font-semibold text-gray-500 shrink-0">
                                                    @if (!empty($item->foto))
                                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <span class="font-medium text-gray-700">{{ $item->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->nip ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->username ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->unit_kerja ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item->ruangan ?? '-' }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $item->role === 'admin' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                                                {{ $item->role === 'admin' ? 'Admin' : 'Pegawai' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('admin.pegawai.edit', $item->id) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.pegawai.reset-password', $item->id) }}" onsubmit="return confirm('Reset password {{ $item->name }} ke password default?')">
                                                    @csrf
                                                    <button type="submit" class="text-amber-500 hover:text-amber-700" title="Reset Password">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                @if ($item->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.pegawai.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus akun {{ $item->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                            Belum ada data pegawai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (isset($pegawai) && method_exists($pegawai, 'links'))
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $pegawai->links() }}
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