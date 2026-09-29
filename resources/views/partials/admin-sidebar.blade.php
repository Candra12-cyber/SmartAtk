{{-- SIDEBAR ADMIN REUSABLE PARTIAL (responsif: off-canvas di mobile, collapse di desktop) --}}

{{-- Tombol hamburger (z-index diturunkan ke z-20 agar tertutup sidebar z-40) --}}
<button id="mobileSidebarToggle" type="button"
    class="no-print lg:hidden fixed top-4 left-4 z-20 bg-[#0B1E3F] text-white p-2.5 rounded-lg shadow-lg">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

{{-- Overlay gelap di belakang sidebar --}}
<div id="sidebarBackdrop" class="no-print lg:hidden fixed inset-0 bg-black/50 z-30 hidden"></div>

<aside id="adminSidebar"
    class="w-64 bg-[#0B1E3F] text-white flex flex-col shrink-0 no-print
           fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-300
           lg:static lg:translate-x-0 lg:z-auto lg:transition-all">
    <div class="px-6 pt-[26px] pb-6 border-b border-white/10 flex items-center gap-3 relative">
        <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <div class="sidebar-label min-w-0">
            <p class="font-bold text-sm leading-tight whitespace-nowrap">Sistem<br>Permintaan ATK</p>
            <p class="text-[10px] text-white/50 mt-1 whitespace-nowrap">Panel Admin</p>
        </div>

        {{-- Tombol collapse, hanya berguna & tampil di desktop --}}
        <button id="toggleSidebarBtn" type="button"
            class="no-print hidden lg:flex absolute -right-3 top-8 w-6 h-6 bg-blue-600 hover:bg-blue-700 rounded-full items-center justify-center text-white shadow-md shrink-0"
            title="Tutup/buka sidebar">
            <svg id="toggleSidebarIcon" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/5' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-label whitespace-nowrap">Dashboard</span>
        </a>
        <a href="{{ route('admin.permintaan.index') }}" title="Kelola Permintaan" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.permintaan.*') ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/5' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="sidebar-label whitespace-nowrap">Kelola Permintaan</span>
            @if (($jumlahMenunggu ?? 0) > 0)
                <span class="sidebar-label ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $jumlahMenunggu }}</span>
            @endif
        </a>
        <a href="{{ route('admin.aatk.index') }}" title="Kelola ATK" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.aatk.*') ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/5' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="sidebar-label whitespace-nowrap">Kelola ATK</span>
        </a>
        <a href="{{ route('admin.pegawai.index') }}" title="Kelola Pegawai" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.pegawai.*') ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/5' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3c0 1.657-3.134 3-7 3s-7-1.343-7-3" />
            </svg>
            <span class="sidebar-label whitespace-nowrap">Kelola Pegawai</span>
        </a>
        <a href="{{ route('admin.laporan.index') }}" title="Laporan" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.laporan.*') ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/5' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="sidebar-label whitespace-nowrap">Laporan</span>
        </a>
    </nav>
</aside>

<style>
    /* Collapse (desktop saja) */
    #adminSidebar.sidebar-collapsed {
        width: 5rem;
    }
    #adminSidebar.sidebar-collapsed .sidebar-label {
        display: none;
    }
    #adminSidebar.sidebar-collapsed nav a {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }
    #adminSidebar.sidebar-collapsed #toggleSidebarIcon {
        transform: rotate(180deg);
    }
</style>

<script>
    (function () {
        var sidebar = document.getElementById('adminSidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        var toggleBtn = document.getElementById('mobileSidebarToggle');
        var collapseBtn = document.getElementById('toggleSidebarBtn');
        if (!sidebar) return;

        // ===== Collapse sidebar (khusus desktop) =====
        if (collapseBtn) {
            if (localStorage.getItem('adminSidebarCollapsed') === '1') {
                sidebar.classList.add('sidebar-collapsed');
            }
            collapseBtn.addEventListener('click', function () {
                var collapsed = sidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem('adminSidebarCollapsed', collapsed ? '1' : '0');
            });
        }

        // ===== Buka/tutup sidebar (khusus mobile) =====
        function isMobileOpen() {
            return sidebar.classList.contains('translate-x-0');
        }
        function openMobileSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            if (backdrop) backdrop.classList.remove('hidden');
        }
        function closeMobileSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            if (backdrop) backdrop.classList.add('hidden');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                isMobileOpen() ? closeMobileSidebar() : openMobileSidebar();
            });
        }

        if (backdrop) backdrop.addEventListener('click', closeMobileSidebar);

        // Tutup otomatis kalau salah satu menu diklik (khusus mobile)
        sidebar.querySelectorAll('nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1024) closeMobileSidebar();
            });
        });

        // Reset state jika di-resize ke desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                if (backdrop) backdrop.classList.add('hidden');
            }
        });
    })();
</script>