<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Dashboard pegawai.
     */
    public function dashboard()
    {
        $userId = auth()->id();
        $query = Permintaan::where('user_id', $userId);

        $totalPermintaan = (clone $query)->count();
        $menunggu = (clone $query)->where('status', 'Menunggu')->count();
        $disetujui = (clone $query)->where('status', 'Disetujui')->count();
        $selesai = (clone $query)->where('status', 'Selesai')->count();
        $permintaanTerbaru = (clone $query)->with('items')->latest()->take(5)->get();

        return view('User.dashboard', compact(
            'totalPermintaan',
            'menunggu',
            'disetujui',
            'selesai',
            'permintaanTerbaru'
        ));
    }

    /**
     * Tampilkan halaman profil.
     */
    public function index()
    {
        return view('User.profil');
    }

    /**
     * Tampilkan form edit profil.
     */
    public function edit()
    {
        return view('User.profil-edit');
    }

    /**
     * Proses update data profil.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . auth()->id()],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'username', 'email', 'jabatan', 'unit_kerja', 'telepon']);

        if ($request->hasFile('foto')) {
            if (auth()->user()->foto) {
                Storage::disk('public')->delete(auth()->user()->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-profil', 'public');
        }

        auth()->user()->update($data);

        return redirect()->route('profil')->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Proses update foto profil saja (dari tombol pensil di halaman Profil).
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        if (auth()->user()->foto) {
            Storage::disk('public')->delete(auth()->user()->foto);
        }

        $path = $request->file('foto')->store('foto-profil', 'public');
        auth()->user()->update(['foto' => $path]);

        return redirect()->route('profil')->with('status', 'Foto profil berhasil diperbarui.');
    }

     /**
     * Hapus foto profil (kembali ke avatar default).
     */
    public function hapusFoto()
    {
        if (auth()->user()->foto) {
            Storage::disk('public')->delete(auth()->user()->foto);
            auth()->user()->update(['foto' => null]);
        }

        return redirect()->route('profil')->with('status', 'Foto profil berhasil dihapus.');
    }

    /**
     * Tampilkan form ubah password.
     */
    public function ubahPasswordForm()
    {
        return view('User.profil-ubah-password');
    }

    /**
     * Proses ubah password.
     */
    public function ubahPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profil')->with('status', 'Password berhasil diubah.');
    }
}