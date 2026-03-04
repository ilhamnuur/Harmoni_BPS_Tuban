<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST ANGGOTA
    |--------------------------------------------------------------------------
    */

    public function anggotaIndex(Request $request)
    {
        $query = User::with('team');
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhereHas('team', function($t) use ($request) {
                      $t->where('nama_tim', 'like', '%' . $request->search . '%');
                  });
            });
        }
        $anggota = $query->latest()->paginate(10);
        return view('anggota.index', compact('anggota'));
    }

    public function anggotaCreate()
    {
        $teams = Team::all();
        return view('anggota.create', compact('teams'));
    }

    public function anggotaStore(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username|max:50',
            'password'     => 'required|min:6',
            'role'         => 'required',
            'team_id'      => 'nullable|exists:teams,id'
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'team_id'      => $request->team_id,
        ]);
        return redirect()->route('manajemen.anggota')->with('success', 'Anggota berhasil didaftarkan!');
    }

    public function anggotaEdit($id)
    {
        $user = User::findOrFail($id);
        $teams = Team::all();
        return view('anggota.edit', compact('user', 'teams'));
    }

    public function anggotaUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:50|unique:users,username,' . $user->id,
            'role'         => 'required',
            'team_id'      => 'nullable|exists:teams,id',
            'password'     => 'nullable|min:6',
        ]);

        $data = $request->only(['nama_lengkap', 'username', 'role', 'team_id']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('manajemen.anggota')->with('success', 'Data anggota diperbarui!');
    }

    public function anggotaDestroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }
        $user->delete();
        return back()->with('success', 'Anggota berhasil dihapus!');
    }
}