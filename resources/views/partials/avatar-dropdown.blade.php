{{--
    KOMPONEN AVATAR DROPDOWN
    Cara pakai: @include('partials.avatar-dropdown')
    Menggantikan menu "Profil" & "Logout" yang sebelumnya di sidebar.
--}}
<div class="relative">
    <button type="button" onclick="toggleAvatarDropdown()" id="btn-avatar"
        class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center text-xs font-semibold text-gray-500 hover:ring-2 hover:ring-blue-300 transition">
        @if (!empty(auth()->user()->foto))
            <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="w-full h-full object-cover">
        @else
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        @endif
    </button>

    <div id="dropdown-avatar" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl border border-gray-100 shadow-lg z-50">
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? '-' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '-' }}</p>
        </div>
        <div class="py-1">
            <a href="{{ route('profil') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Saya
            </a>
        </div>
        <div class="border-t border-gray-100 py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 text-left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

@once
<script>
    function toggleAvatarDropdown() {
        document.getElementById('dropdown-avatar').classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const btn = document.getElementById('btn-avatar');
        const dropdown = document.getElementById('dropdown-avatar');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>
@endonce