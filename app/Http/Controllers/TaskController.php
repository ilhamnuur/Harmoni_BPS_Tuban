<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaPhoto;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST TASK (PENDING)
    |--------------------------------------------------------------------------
    */

    public function taskIndex()
    {
        $user = auth()->user();
        $tugas = Agenda::with(['activityType', 'creator'])
            ->where('assigned_to', $user->id)
            ->where('activity_type_id', 1) 
            ->where('status_laporan', 'Pending')
            ->latest()->get();

        return view('task.index', compact('tugas'));
    }

    public function taskCreate($id) 
    {
        $user = auth()->user();
        $today = date('Y-m-d');
        $sedangCuti = Absensi::where('user_id', $user->id)->where('status', 'Cuti')
            ->where('start_date', '<=', $today)->where('end_date', '>=', $today)->exists();

        if ($sedangCuti) {
            return redirect()->route('task.index')->with('error', 'Anda sedang CUTI.');
        }

        $agenda = Agenda::with(['activityType', 'team'])->where('id', $id)
            ->where('assigned_to', $user->id)->firstOrFail();

        return view('task.create', compact('agenda'));
    }

    public function taskStore(Request $request, $id) 
    {
        $agenda = Agenda::where('id', $id)->where('assigned_to', auth()->id())->firstOrFail();
        $request->validate([
            'tanggal_pelaksanaan' => ['required', 'date'],
            'responden' => 'required',
            'aktivitas' => 'required',
            'fotos' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('dokumentasi', 'public');
                    AgendaPhoto::create(['agenda_id' => $agenda->id, 'photo_path' => $path]);
                }
            }
            $agenda->update(array_merge($request->only(['tanggal_pelaksanaan', 'responden', 'aktivitas', 'permasalahan', 'solusi_antisipasi']), ['status_laporan' => 'Selesai']));
            DB::commit();
            return redirect()->route('task.index')->with('success', 'Laporan berhasil terkirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}