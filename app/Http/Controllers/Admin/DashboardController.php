<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangAatk;
use App\Models\Permintaan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahMenunggu = Permintaan::where('status', 'Menunggu')->count();
        $jumlahPegawai = User::where('role', 'user')->count();
        $jumlahMenipis = BarangAatk::where('stok', '<=', 15)->count();
        $jumlahSelesai = Permintaan::where('status', 'Selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $permintaanMenunggu = Permintaan::where('status', 'Menunggu')
            ->with(['items', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'jumlahMenunggu',
            'jumlahPegawai',
            'jumlahMenipis',
            'jumlahSelesai',
            'permintaanMenunggu'
        ));
    }
}