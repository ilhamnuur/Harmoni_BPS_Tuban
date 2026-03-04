<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        // Jika user sudah login, jangan kasih ke halaman login lagi, lempar ke dashboard
        if (Auth::check()) {
            return redirect()->intended('dashboard');
        }
        return view('auth.login');
    }

    // Proses login
    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // 1. Bersihkan session lama dan buat yang baru (Penting untuk cegah 419)
            $request->session()->regenerate();

            // 2. Redirect ke intended (halaman yang dituju sebelumnya) atau ke dashboard
            return redirect()->intended('dashboard')->with('success', 'Selamat Datang Kembali!');
        }

        // Jika gagal, kembali dengan error
        return back()->with('error', 'Username atau Password salah!')->withInput();
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Bersihkan semua data session agar benar-benar fresh
        $request->session()->invalidate();

        // Buat token CSRF baru agar login berikutnya tidak 419
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil Keluar.');
    }
}