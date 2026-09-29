<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm">
        @csrf

        {{-- Logo & Header --}}
        <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/logo-pn-garut.png') }}" alt="Logo Pengadilan Negeri Garut" class="w-1000 h-1000 object-contain">
            
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 sm:p-8">
            <h2 class="text-center text-base font-bold text-gray-800 mb-1">Login Pegawai</h2>
            <p class="text-center text-xs text-blue-600 mb-6 leading-relaxed">
                Silakan masukkan kredensial Anda untuk melanjutkan.
            </p>

            {{-- Alert error --}}
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Email / Username --}}
            <div class="mb-4">
                <label for="username" class="block text-xs font-semibold text-gray-700 mb-1">Email / Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan Email atau Username"
                        required
                        autofocus
                        class="w-full text-sm border border-gray-300 rounded-lg py-2.5 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Masukkan Password"
                        required
                        class="w-full text-sm border border-gray-300 rounded-lg py-2.5 pl-10 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                    >
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Ingat saya & Lupa password --}}
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Ingat saya
                </label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-700 hover:underline">
                    Lupa Password?
                </a>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors"
            >
                Masuk
            </button>
        </div>

        {{-- Footer --}}
        <p class="text-center text-[11px] text-gray-400 mt-6">
            &copy; {{ date('Y') }} Pengadilan Negeri Garut. v1.0.2
        </p>
    </form>

    {{-- Dekorasi titik-titik bawah (opsional, sesuai desain asli) --}}
    <div class="fixed bottom-0 left-0 w-full h-16 bg-dots opacity-30 pointer-events-none"></div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>
</body>
</html>