<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'         => ['required', Rule::in(['Pegawai', 'Katim'])], // Admin tidak boleh ubah role sendiri ke bawah via sini
            'password'     => 'nullable|min:8|confirmed',
        ]);

        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;
        
        // Logika Ganti Role: Hanya jika bukan Admin (Admin biarlah tetap Admin)
        if ($user->role != 'Admin') {
            $user->role = $request->role;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil dan peran berhasil diperbarui!');
    }
}