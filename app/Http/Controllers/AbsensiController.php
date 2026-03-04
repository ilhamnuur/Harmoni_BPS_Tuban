<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN ABSENSI (KHUSUS SUBBAGIAN UMUM)
    |--------------------------------------------------------------------------
    */

     public function absensiIndex()
    {
        $user = Auth::user();
        if (!$user->team || $user->team->nama_tim !== 'Subbagian Umum') {
            return redirect()->route('dashboard')->with('error', 'Akses Terbatas! Hanya Tim Subbagian Umum.');
        }

        $anggotaTim = User::orderBy('nama_lengkap', 'asc')->get();
        $absensi = Absensi::with('user')->where('status', 'Cuti')->get();

        return view('absensi.index', compact('anggotaTim', 'absensi'));
    }

    public function absensiStore(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required',
            'keterangan' => 'nullable|string'
        ]);

        Absensi::create([
            'user_id'    => $request->user_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
            'input_by'   => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil ditambahkan!');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: CEK APAKAH USER SUBBAG UMUM
    |--------------------------------------------------------------------------
    */

    private function isSubbagUmum($user): bool
    {
        return $user->team && $user->team->nama_tim === 'Subbagian Umum';
    }
}