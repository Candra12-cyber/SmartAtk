<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
     /**
     * Buat password awal unik per pegawai, berbasis 4 digit terakhir NIP.
     * Tetap gampang disampaikan admin ke pegawai secara lisan/tertulis.
     */
    private function buatPasswordAwal(?string $nip): string
    {
        $nip = trim((string) $nip);

        // Kalau NIP kosong atau kurang dari 4 digit, jangan pakai pola
        // yang gampang ditebak — pakai random string sebagai gantinya.
        if (strlen($nip) < 4) {
            return 'PNGarut-' . \Illuminate\Support\Str::random(6);
        }

        $ekorNip = substr($nip, -4);
        return 'PNGarut-' . $ekorNip . '!';
    }
    /**
     * Daftar semua pegawai.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', "%{$cari}%")
                  ->orWhere('nip', 'like', "%{$cari}%")
                  ->orWhere('username', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $pegawai = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.pegawai-index', compact('pegawai'));
    }

    /**
     * Form tambah pegawai baru.
     */
    public function create()
    {
        return view('admin.pegawai-form');
    }

    /**
     * Simpan pegawai baru dengan password default.
     */
public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'unique:users,nip'],
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,user'],
            'ruangan' => ['nullable', 'string', 'max:255'],

        ]);

        $passwordAwal = $this->buatPasswordAwal($request->nip);

        User::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'unit_kerja' => $request->unit_kerja,
            'telepon' => $request->telepon,
            'role' => $request->role,
            'password' => Hash::make($passwordAwal),
            'ruangan' => $request->ruangan,
        ]);

        return redirect()->route('admin.pegawai.index')->with('status', 'Pegawai berhasil ditambahkan. Password awal: ' . $passwordAwal);
    }

    /**
     * Form edit pegawai.
     */
    public function edit($id)
    {
        $pegawai = User::findOrFail($id);

        return view('admin.pegawai-form', compact('pegawai'));
    }

    /**
     * Update data pegawai.
     */
    public function update(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'unique:users,nip,' . $id],
            'username' => ['required', 'string', 'unique:users,username,' . $id],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,user'],
            'ruangan' => ['nullable', 'string', 'max:255'],
        ]);

        $pegawai->update($request->only([
            'name', 'nip', 'username', 'email', 'jabatan', 'unit_kerja', 'telepon', 'role', 'ruangan',
        ]));

        return redirect()->route('admin.pegawai.index')->with('status', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Reset password pegawai ke password default.
     */
    public function resetPassword($id)
    {
        $pegawai = User::findOrFail($id);
        $passwordBaru = $this->buatPasswordAwal($pegawai->nip);
        $pegawai->update(['password' => Hash::make($passwordBaru)]);

        return redirect()->route('admin.pegawai.index')
            ->with('status', 'Password berhasil direset.')
            ->with('password_baru', $passwordBaru)
            ->with('nama_pegawai', $pegawai->name);
    }

    /**
     * Hapus akun pegawai.
     */
    public function destroy($id)
    {
        if ((int) $id === auth()->id()) {
            return back()->with('status', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $pegawai = User::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')->with('status', 'Pegawai berhasil dihapus.');
    }
}