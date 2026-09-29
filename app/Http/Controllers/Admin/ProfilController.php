<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        return view('admin.profil');
    }

    public function edit()
    {
        return view('admin.profil-edit');
    }

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

        return redirect()->route('admin.profil')->with('status', 'Profil berhasil diperbarui.');
    }

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

        return redirect()->route('admin.profil')->with('status', 'Foto profil berhasil diperbarui.');
    }

    public function hapusFoto()
    {
        if (auth()->user()->foto) {
            Storage::disk('public')->delete(auth()->user()->foto);
            auth()->user()->update(['foto' => null]);
        }

        return redirect()->route('admin.profil')->with('status', 'Foto profil berhasil dihapus.');
    }

    public function ubahPasswordForm()
    {
        return view('admin.profil-ubah-password');
    }

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

        return redirect()->route('admin.profil')->with('status', 'Password berhasil diubah.');
    }
}