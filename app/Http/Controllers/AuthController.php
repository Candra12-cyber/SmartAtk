<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        // kalau sudah login, langsung arahkan sesuai role
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     * Login bisa pakai Email atau Username (NIP tidak dipakai untuk login).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Deteksi otomatis: kalau formatnya email, cek kolom 'email'; kalau tidak, cek kolom 'username'.
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $attempt = [
            $loginField => $credentials['username'],
            'password'  => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (! Auth::attempt($attempt, $remember)) {
            throw ValidationException::withMessages([
                'username' => 'Email/Username atau password yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user()->role);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Arahkan user ke dashboard sesuai role-nya.
     */
    protected function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}