<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Permintaan AATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; }
        .bg-dots {
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 14px 14px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center py-10 px-4">

    <div class="w-full max-w-sm">

        {{-- Logo & Header --}}
        <div class="flex flex-col items-center mb-6">
            <div class="w-20 h-20 rounded-full bg-white shadow flex items-center justify-center overflow-hidden mb-4">
                <img src="{{ asset('images/logo-pn-garut.png') }}" alt="Logo Pengadilan Negeri Garut" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-lg font-bold text-blue-900 text-center">Pengadilan Negeri Garut</h1>
            <p class="text-sm text-orange-500 font-medium">Sistem Permintaan AATK</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 sm:p-8">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-base font-bold text-gray-800">Lupa Password</h2>
            </div>

            <div class="flex flex-col items-center text-center py-4">
                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-1">
                    Untuk alasan keamanan, reset password tidak dilakukan secara otomatis.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Silakan hubungi <span class="font-semibold text-gray-800">Admin Sistem</span> untuk mengajukan permintaan reset password Anda.
                </p>
            </div>

            <hr class="border-gray-100 my-5">

            {{-- Info Kontak Admin --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-gray-400">Telepon / WhatsApp Admin</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $kontakAdmin['telepon'] ?? '+62 811 1111 1111' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-gray-400">Email Admin</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $kontakAdmin['email'] ?? 'admin@pn-garut.go.id' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21h2m0 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-4a1 1 0 011-1h0a1 1 0 011 1v4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-gray-400">Lokasi</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $kontakAdmin['lokasi'] ?? 'Bagian Umum, Lantai 1' }}</p>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-gray-400 mt-5 leading-relaxed">
                Mohon siapkan NIP Anda saat menghubungi admin untuk mempercepat proses verifikasi.
            </p>

            <a href="{{ route('login') }}" class="mt-5 block text-center w-full border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">
                Kembali ke Login
            </a>
        </div>

        <p class="text-center text-[11px] text-gray-400 mt-6">
            &copy; {{ date('Y') }} Pengadilan Negeri Garut. v1.0.2
        </p>
    </div>

    <div class="fixed bottom-0 left-0 w-full h-16 bg-dots opacity-30 pointer-events-none"></div>
</body>
</html>