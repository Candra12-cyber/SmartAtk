<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Admin - Sistem Permintaan ATK</title>
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
                    <a href="{{ route('admin.profil') }}" class="hover:text-blue-600">Profil</a>
                    <span>›</span>
                    <span class="text-blue-600 font-medium">Edit Profil</span>
                </nav>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6">

                @php $user = auth()->user(); @endphp

                <div class="max-w-2xl">
                    <h2 class="text-xl font-bold text-gray-800 mb-1">Edit Profil</h2>
                    <p class="text-sm text-gray-500 mb-6">Perbarui informasi data diri Anda.</p>

                    @if (session('status'))
                        <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- FOTO PROFIL --}}
                        <div class="flex items-center gap-5 pb-2">
                            <div class="w-20 h-20 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center text-xl font-bold text-gray-400 shrink-0" id="preview-wrapper">
                                @if (!empty($user->foto))
                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" id="preview-foto" class="w-full h-full object-cover">
                                @else
                                    <span id="preview-inisial">{{ strtoupper(substr($user->name ?? 'B', 0, 1)) }}</span>
                                    <img src="" alt="Foto Profil" id="preview-foto" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Ganti Foto
                                    <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="previewFoto(this)">
                                </label>
                                @if (!empty($user->foto))
                                    <button type="submit" form="form-hapus-foto-profil" class="ml-2 inline-flex items-center text-xs font-semibold text-red-500 hover:text-red-700">
                                        Hapus Foto
                                    </button>
                                @endif
                                <p class="text-[11px] text-gray-400 mt-1.5">Format JPG/PNG, maksimal 2MB.</p>
                            </div>
                        </div>

                        <hr class="border-gray-100">


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">NIP</label>
                                <input type="text" value="{{ $user->nip }}" disabled
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-400 cursor-not-allowed">
                                <p class="text-[11px] text-gray-400 mt-1">NIP tidak dapat diubah. Hubungi admin jika ada kesalahan.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan</label>
                                <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Unit Kerja</label>
                                <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Telepon</label>
                                <input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.profil') }}" class="border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    {{-- Form terpisah khusus buat tombol "Hapus Foto" di atas (nggak bisa ditaruh di dalam form utama karena form tidak boleh bersarang) --}}
                    @if (!empty($user->foto))
                        <form id="form-hapus-foto-profil" method="POST" action="{{ route('admin.profil.foto.hapus') }}"
                            onsubmit="return confirm('Hapus foto profil? Kembali ke avatar default.');" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-gray-200 px-6 py-3 text-[11px] text-gray-400 bg-white">
                &copy; {{ date('Y') }} Pengadilan Negeri Garut - v1.0.2
            </footer>
        </div>
    <script>
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('preview-foto');
                    const inisial = document.getElementById('preview-inisial');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    if (inisial) inisial.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>