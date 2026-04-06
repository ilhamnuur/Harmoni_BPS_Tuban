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
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // --- 1. IDENTIFIKASI PEJABAT ASLI ---
    $daftarPejabat = [
        'kepala.bps', 'ketua.tim', 'dodik.hendarto', 'respati.yekti', 
        'umdatul.ummah', 'ika.rahmawati', 'arif.suroso', 'triana.puji', 
        'yudhi.prasetyono', 'wicaksono'
    ];

    $isPejabatAsli = in_array($user->username, $daftarPejabat);

    // --- 2. LOGIKA ROLE YANG DIIZINKAN ---
    $allowedRoles = ['Pegawai'];

    if ($isPejabatAsli && ($request->has_super_access == 1 || $user->role !== 'Pegawai')) {
        if ($user->team_id == 8) {
            $allowedRoles = ['Kepala', 'Pegawai'];
        } else {
            $allowedRoles = ['Katim', 'Pegawai'];
        }
    } 
    
    if ($user->role === 'Admin') {
        $allowedRoles = ['Admin'];
    }

    // --- 3. VALIDASI (DITAMBAHKAN VALIDASI SIGNATURE) ---
    $request->validate([
        'nama_lengkap'     => 'required|string|max:255',
        'username'         => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        'role'             => ['required', \Illuminate\Validation\Rule::in($allowedRoles)],
        'password'         => 'nullable|min:8|confirmed',
        'signature'        => 'nullable|image|mimes:png|max:2048', // Khusus PNG agar transparan, max 2MB
    ]);

    // --- 4. UPDATE DATA DASAR ---
    $user->nama_lengkap = $request->nama_lengkap;
    $user->username = $request->username;

    // --- 5. LOGIKA UPLOAD TANDA TANGAN (KHUSUS KEPALA & KATIM) ---
    if ($request->hasFile('signature')) {
        // Hanya izinkan jika role saat ini atau role baru adalah Kepala/Katim
        if (in_array($request->role, ['Kepala', 'Katim'])) {
            
            // Hapus TTD lama jika ada di storage
            if ($user->signature && \Storage::disk('public')->exists($user->signature)) {
                \Storage::disk('public')->delete($user->signature);
            }

            // Simpan TTD baru ke folder 'signatures'
            $path = $request->file('signature')->store('signatures', 'public');
            $user->signature = $path;
        }
    }

    // --- 6. LOGIKA AKSES SUPER ---
    $hasSuper = $request->input('has_super_access', 0);
    if ($request->role === 'Kepala' || $request->role === 'Katim') {
        $hasSuper = 1;
    }
    $user->has_super_access = (int) $hasSuper;

    // --- 7. UPDATE ROLE & PASSWORD ---
    if ($user->role !== 'Admin') {
        $user->role = $request->role;
    }

    if ($request->filled('password')) {
        $user->password = \Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profil dan Tanda Tangan berhasil diperbarui!');
}
}