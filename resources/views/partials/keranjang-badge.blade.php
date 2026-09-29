{{--
    KOMPONEN KERANJANG
    Cara pakai: @include('partials.keranjang-badge')
--}}
@php
    $jumlahKeranjang = collect(session('keranjang', []))->count();
@endphp

<a href="{{ route('keranjang.index') }}" class="relative inline-flex items-center text-gray-400 hover:text-gray-600" title="Keranjang">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-9 3a1 1 0 102 0 1 1 0 00-2 0zm9 0a1 1 0 102 0 1 1 0 00-2 0z" />
    </svg>
    <span data-keranjang-badge
        class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center {{ $jumlahKeranjang > 0 ? '' : 'hidden' }}">
        {{ $jumlahKeranjang > 9 ? '9+' : $jumlahKeranjang }}
    </span>
</a>