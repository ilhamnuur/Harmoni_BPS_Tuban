@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Sederhana --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Agenda Rapat</h4>
            <p class="text-muted small mb-0">Kelola kehadiran dan notulensi rapat dinas Anda.</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-4 shadow-sm border border-primary border-opacity-10">
            <i class="fas fa-calendar-alt text-primary me-2"></i>
            <span class="fw-bold small text-dark">{{ $meetings->count() }} Agenda Aktif</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 150px;">Tanggal</th>
                        <th class="border-0 py-3">Rapat / Lokasi</th>
                        <th class="border-0 py-3">Penyelenggara</th>
                        <th class="border-0 py-3 text-center" style="width: 160px;">Status Anda</th>
                        <th class="border-0 py-3 text-center" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $m)
                        @php
                            $isOverdue = \Carbon\Carbon::parse($m->event_date)->isPast() && !$m->event_date->isToday();
                            $sudahTTD = \App\Models\MeetingPresence::where('agenda_id', $m->id)
                                        ->where('user_id', Auth::id())
                                        ->exists();
                        @endphp
                        <tr class="transition-row">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($m->event_date)->translatedFormat('d M Y') }}</div>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $m->start_time ?? '--:--' }} WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary mb-1">{{ $m->title }}</div>
                                <div class="small text-muted">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $m->location }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.7rem; font-weight: 800;">
                                        {{ strtoupper(substr($m->creator->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="small fw-semibold">{{ $m->creator->nama_lengkap ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($sudahTTD)
                                    <span class="badge bg-success-subtle text-success rounded-pill border border-success border-opacity-25 shadow-xs status-badge">
                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                    </span>
                                @elseif($isOverdue)
                                    <span class="badge bg-danger-subtle text-danger rounded-pill border border-danger border-opacity-25 shadow-xs status-badge">
                                        <i class="fas fa-times-circle me-1"></i> Terlewat
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill border border-warning border-opacity-25 shadow-xs status-badge">
                                        <i class="fas fa-clock me-1"></i> Belum Absen
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- Tombol Presensi --}}
                                    @if(!$sudahTTD && (!$isOverdue || $m->event_date->isToday()))
                                        <a href="{{ route('meeting.presensi', $m->id) }}" class="btn btn-primary btn-custom-action fw-bold shadow-sm" title="Isi Daftar Hadir">
                                            <i class="fas fa-signature me-1"></i> Absen
                                        </a>
                                    @endif

                                    {{-- Tombol Notulensi (Jika Notulis) --}}
                                    @if($m->notulis_id == Auth::id())
                                        <a href="{{ route('meeting.notulensi', $m->id) }}" class="btn btn-dark btn-sm btn-custom-action fw-bold" title="Input Notulensi">
                                            <i class="fas fa-pen-nib me-1"></i> Notulis
                                        </a>
                                    @endif

                                    {{-- Tombol Monitoring (Jika Punya Akses) --}}
                                    @if($m->user_id == Auth::id() || Auth::user()->role == 'Admin' || Auth::user()->role == 'Katim')
                                        <a href="{{ route('meeting.monitoring', $m->id) }}" class="btn btn-light btn-sm btn-custom-action border shadow-xs" title="Monitoring Kehadiran">
                                            <i class="fas fa-desktop me-1 text-muted"></i> Pantau
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/amber/no-data.svg" style="height: 120px;" class="mb-3 opacity-50">
                                <h6 class="fw-bold text-muted">Belum Ada Agenda Rapat</h6>
                                <p class="text-muted small">Saat ini tidak ada jadwal rapat terdekat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* CSS BARU: Ukuran Status Badge yang Seragam */
    .status-badge {
        width: 110px; /* Lebar tetap untuk kolom status */
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem !important;
        padding: 0 !important;
    }

    /* Ukuran Tombol Aksi yang Seragam */
    .btn-custom-action {
        width: 90px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem !important;
        border-radius: 8px !important;
        padding: 0 !important;
        white-space: nowrap;
    }

    .bg-primary-subtle { background-color: #eef6ff; }
    .bg-success-subtle { background-color: #f0fdf4; }
    .bg-danger-subtle { background-color: #fef2f2; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .text-success { color: #16a34a !important; }
    .text-danger { color: #dc2626 !important; }
    .text-warning-emphasis { color: #92400e !important; }
    
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .transition-row { transition: all 0.2s ease; }
    .transition-row:hover { background-color: #f8fafc !important; }
    
    .table thead th {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
    }
    
    .btn-primary { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); border: none; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 88, 168, 0.2); }
</style>
@endsection