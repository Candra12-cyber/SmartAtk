<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($pegawai) ? 'Edit' : 'Tambah' }} Pegawai - Sistem Permintaan ATK</title>
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
                <nav class="text-xs text-gray-500 flex items-center gap-1.5">
                    <a href="{{ route('admin.pegawai.index') }}" class="hover:text-blue-600">Kelola Pegawai</a>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">{{ isset($pegawai) ? 'Edit Pegawai' : 'Tambah Pegawai' }}</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6">
                <div class="max-w-xl">
                    <h2 class="text-xl font-bold text-gray-800 mb-1">
                        {{ isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Pegawai Baru' }}
                    </h2>
                    <p class="text-sm text-gray-500 mb-6">
                        {{ isset($pegawai) ? 'Perbarui informasi akun pegawai.' : 'Buat akun baru untuk pegawai. Password default akan digunakan.' }}
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

                    <form method="POST" action="{{ isset($pegawai) ? route('admin.pegawai.update', $pegawai->id) : route('admin.pegawai.store') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        @csrf
                        @if (isset($pegawai))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $pegawai->name ?? '') }}" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip', $pegawai->nip ?? '') }}" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username</label>
                                <input type="text" name="username" value="{{ old('username', $pegawai->username ?? '') }}" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input type="email" name="email" value="{{ old('email', $pegawai->email ?? '') }}" required
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan</label>
                                <input type="text" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan ?? '') }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Unit Kerja</label>
                                <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $pegawai->unit_kerja ?? '') }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ruangan</label>
                                <input type="text" name="ruangan" value="{{ old('ruangan', $pegawai->ruangan ?? '') }}" placeholder="Contoh: Lantai 2, Ruang Pidana 1"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Telepon</label>
                                <input type="text" name="telepon" value="{{ old('telepon', $pegawai->telepon ?? '') }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role</label>
                                <select name="role" required class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @php $roleTerpilih = old('role', $pegawai->role ?? 'user'); @endphp
                                    <option value="user" {{ $roleTerpilih == 'user' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="admin" {{ $roleTerpilih == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                        </div>

                        @unless (isset($pegawai))
                            <div class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-blue-700">Akun akan dibuat dengan password default <span class="font-mono font-semibold">password123</span>. Sampaikan ke pegawai untuk segera menggantinya.</p>
                            </div>
                        @endunless

                        <hr class="border-gray-100">

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.pegawai.index') }}" class="border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                                {{ isset($pegawai) ? 'Simpan Perubahan' : 'Tambah Pegawai' }}
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
</body>
</html>