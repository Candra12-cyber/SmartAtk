<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Sistem Permintaan ATK</title>
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
            
            <header class="bg-white border-b border-gray-200 pl-16 pr-6 py-3 lg:px-6 flex items-center justify-between">                <h1 class="text-base font-semibold text-gray-800">Profil Admin</h1>
                <div class="flex items-center gap-4">
                    @include('partials.admin-avatar-dropdown')
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-6">

                @if (session('status'))
                    <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                @php $user = auth()->user(); @endphp

                <div class="max-w-3xl bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

                    {{-- COVER + AVATAR --}}
                    <div class="h-28 bg-gray-100"></div>
                    <div class="px-8">
                        <div class="-mt-14 relative w-28 h-28 mx-auto">
                            <div class="w-28 h-28 rounded-full bg-white p-1 shadow">
                                <div class="w-full h-full rounded-full bg-gray-200 overflow-hidden flex items-center justify-center text-2xl font-bold text-gray-400">
                                    @if (!empty($user->foto))
                                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="w-full h-full object-cover rounded-full">
                                    @else
                                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.profil.foto.update') }}" enctype="multipart/form-data" id="form-foto-profil">
                                @csrf
                                <label class="absolute bottom-1 right-1 w-7 h-7 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94l-3.11 1.243a.5.5 0 01-.65-.65l1.243-3.11a4 4 0 01.94-1.414z" />
                                    </svg>
                                    <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="document.getElementById('form-foto-profil').submit()">
                                </label>
                            </form>
                        </div>

                        <div class="text-center mt-4">
                            <h2 class="text-lg font-bold text-gray-800">{{ $user->name ?? '-' }}</h2>
                            <p class="text-xs text-gray-400 mt-1">{{ $user->email ?? '-' }}</p>
                            <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-600">Administrator</span>
                        </div>

                        {{-- INFO GRID --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
                            <div class="bg-gray-50 rounded-xl p-4 flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">Username</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->username ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-4 6v8a2 2 0 002 2h12a2 2 0 002-2v-8H4z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">Jabatan</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->jabatan ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21h2m0 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-4a1 1 0 011-1h0a1 1 0 011 1v4" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">Unit Kerja</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->unit_kerja ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">Nomor Telepon</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->telepon ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-100">

                        {{-- ACTION BUTTONS --}}
                        <div class="flex items-center justify-center gap-3 pb-8">
                            <a href="{{ route('admin.profil.edit') }}" class="flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profil
                            </a>
                            <a href="{{ route('admin.profil.ubah-password') }}" class="flex items-center gap-2 text-blue-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Ubah Password
                            </a>
                        </div>
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