{{--
    KOMPONEN NOTIFIKASI
    Cara pakai: cukup ganti ikon lonceng statis di setiap halaman dengan:
    @include('partials.notifikasi-dropdown')
--}}
@php
    $daftarNotifikasi = auth()->user()->notifikasi()->latest()->take(8)->get();
    $jumlahBelumDibaca = auth()->user()->notifikasi()->where('dibaca', false)->count();

    $tipeWarna = [
        'info' => 'bg-blue-100 text-blue-600',
        'sukses' => 'bg-green-100 text-green-600',
        'peringatan' => 'bg-amber-100 text-amber-600',
        'gagal' => 'bg-red-100 text-red-600',
    ];
    $tipeIcon = [
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'sukses' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'peringatan' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'gagal' => 'M6 18L18 6M6 6l12 12',
    ];
@endphp

<div class="relative">
    <button type="button" onclick="toggleNotifDropdown()" class="relative text-gray-400 hover:text-gray-600" id="btn-notif">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($jumlahBelumDibaca > 0)
            <span class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">
                {{ $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca }}
            </span>
        @endif
    </button>

    {{-- DROPDOWN --}}
    <div id="dropdown-notif" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl border border-gray-100 shadow-lg z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-blue-600 hover:underline">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse ($daftarNotifikasi as $item)
                <a href="{{ route('notifikasi.baca', $item->id) }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 {{ !$item->dibaca ? 'bg-blue-50/40' : '' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $tipeWarna[$item->tipe] ?? $tipeWarna['info'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tipeIcon[$item->tipe] ?? $tipeIcon['info'] }}" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-gray-800 leading-snug">{{ $item->judul }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5 leading-snug line-clamp-2">{{ $item->pesan }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $item->created_at->diffForHumans() }}</p>
                    </div>
                    @if (!$item->dibaca)
                        <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0 mt-1.5"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-xs text-gray-400">Belum ada notifikasi.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Script hanya perlu dimuat sekali per halaman --}}
@once
<script>
    function toggleNotifDropdown() {
        document.getElementById('dropdown-notif').classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const btn = document.getElementById('btn-notif');
        const dropdown = document.getElementById('dropdown-notif');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>
@endonce