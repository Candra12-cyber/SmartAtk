<?php

use App\Http\Controllers\AatkController;
use App\Http\Controllers\Admin\AatkController as AdminAatkController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PegawaiController as AdminPegawaiController;
use App\Http\Controllers\Admin\PermintaanController as AdminPermintaanController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ===== AUTH =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {
    $admin = \App\Models\User::where('role', 'admin')
        ->whereNotNull('telepon')
        ->orderBy('id')
        ->first();

    return view('auth.forgot-password', [
        'kontakAdmin' => [
            'telepon' => $admin->telepon ?? '+62 811 1111 1111',
            'email' => $admin->email ?? 'admin@pn-garut.go.id',
            'lokasi' => $admin->ruangan ?? 'Bagian Umum, Lantai 1',
        ],
    ]);
})->name('password.request');

// ===== USER (hanya role user) =====
Route::middleware(['role:user'])->group(function () {
    Route::get('/user/dashboard', [ProfilController::class, 'dashboard'])->name('user.dashboard');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/edit', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [ProfilController::class, 'updateFoto'])->name('profil.foto.update');
    Route::delete('/profil/foto', [ProfilController::class, 'hapusFoto'])->name('profil.foto.hapus');
    Route::get('/profil/ubah-password', [ProfilController::class, 'ubahPasswordForm'])->name('profil.ubah-password');
    Route::put('/profil/ubah-password', [ProfilController::class, 'ubahPassword'])->name('profil.ubah-password.update');
});

// ===== NOTIFIKASI (User & Admin) =====
Route::middleware(['role:user,admin'])->group(function () {
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::get('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
});

// ===== PERMINTAAN (User & Admin) =====
Route::middleware(['role:user,admin'])->group(function () {
    Route::get('/permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('/permintaan/{id}', [PermintaanController::class, 'show'])->name('permintaan.show');

    Route::get('/daftar-aatk', [AatkController::class, 'index'])->name('aatk.index');

    // Keranjang ATK (sebelum buat permintaan)
    Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang/{id}/tambah', [CartController::class, 'tambah'])->name('keranjang.tambah');
    Route::put('/keranjang/{id}', [CartController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{id}', [CartController::class, 'hapus'])->name('keranjang.hapus');
    Route::delete('/keranjang', [CartController::class, 'kosongkan'])->name('keranjang.kosongkan');

    // Alias URL jika diakses langsung
    Route::get('/buatpermintaan', function () {
        return redirect()->route('permintaan.create');
    });
    Route::get('/buat-permintaan', function () {
        return redirect()->route('permintaan.create');
    });
});

// ===== ADMIN (hanya role admin) =====
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola Permintaan
    Route::get('/permintaan', [AdminPermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('/permintaan/{id}', [AdminPermintaanController::class, 'show'])->name('permintaan.show');
    Route::post('/permintaan/{id}/setujui', [AdminPermintaanController::class, 'setujui'])->name('permintaan.setujui');
    Route::post('/permintaan/{id}/tolak', [AdminPermintaanController::class, 'tolak'])->name('permintaan.tolak');
    Route::post('/permintaan/{id}/status', [AdminPermintaanController::class, 'updateStatus'])->name('permintaan.update-status');

    // Kelola AATK (create SEBELUM {id}, penting untuk urutan route)
    Route::get('/aatk', [AdminAatkController::class, 'index'])->name('aatk.index');
    Route::get('/aatk/create', [AdminAatkController::class, 'create'])->name('aatk.create');
    Route::post('/aatk', [AdminAatkController::class, 'store'])->name('aatk.store');
    Route::get('/aatk/{id}/edit', [AdminAatkController::class, 'edit'])->name('aatk.edit');
    Route::put('/aatk/{id}', [AdminAatkController::class, 'update'])->name('aatk.update');
    Route::delete('/aatk/{id}', [AdminAatkController::class, 'destroy'])->name('aatk.destroy');
    Route::post('/aatk/{id}/tambah-stok', [AdminAatkController::class, 'tambahStok'])->name('aatk.tambah-stok');
    Route::get('/aatk/export-excel', [AdminAatkController::class, 'exportExcel'])->name('aatk.export-excel');
    Route::get('/aatk/export-pdf', [AdminAatkController::class, 'exportPdf'])->name('aatk.export-pdf');

    // Kelola Pegawai (create SEBELUM {id})
    Route::get('/pegawai', [AdminPegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/create', [AdminPegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [AdminPegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{id}/edit', [AdminPegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/pegawai/{id}', [AdminPegawaiController::class, 'update'])->name('pegawai.update');
    Route::post('/pegawai/{id}/reset-password', [AdminPegawaiController::class, 'resetPassword'])->name('pegawai.reset-password');
    Route::delete('/pegawai/{id}', [AdminPegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // Laporan (masih placeholder)
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [AdminLaporanController::class, 'export'])->name('laporan.export');
    Route::get('/laporan/export-detail', [AdminLaporanController::class, 'exportDetail'])->name('laporan.export-detail');
    Route::get('/laporan/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-barang-masuk', [AdminLaporanController::class, 'exportBarangMasuk'])->name('laporan.export-barang-masuk');
    Route::get('/laporan/export-barang-masuk-pdf', [AdminLaporanController::class, 'exportBarangMasukPdf'])->name('laporan.export-barang-masuk-pdf');
    // Profil Admin
    Route::get('/profil', [AdminProfilController::class, 'index'])->name('profil');
    Route::get('/profil/edit', [AdminProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/edit', [AdminProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [AdminProfilController::class, 'updateFoto'])->name('profil.foto.update');
    Route::delete('/profil/foto', [AdminProfilController::class, 'hapusFoto'])->name('profil.foto.hapus');
    Route::get('/profil/ubah-password', [AdminProfilController::class, 'ubahPasswordForm'])->name('profil.ubah-password');
    Route::put('/profil/ubah-password', [AdminProfilController::class, 'ubahPassword'])->name('profil.ubah-password.update');                                                   
});