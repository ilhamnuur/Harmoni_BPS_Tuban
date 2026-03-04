@php
    \Carbon\Carbon::setLocale('id');
    // Mode Default adalah 'month' jika tidak ada di request
    $viewMode = request('view_mode', 'month');
    $startDate = \Carbon\Carbon::create($year, $month, 1);
    
    if($viewMode == 'week') {
        // Ambil hari Senin dari minggu saat ini (atau minggu yang dipilih)
        // Kita gunakan hari ke-1 sebagai patokan awal minggu jika dalam konteks bulan tersebut
        $startDate = \Carbon\Carbon::parse(request('week_start', now()->startOfWeek()->format('Y-m-d')));
        $daysToShow = 7;
    } else {
        $daysToShow = $daysInMonth;
    }
@endphp

@extends('layouts.app')

@section('content')
<style>
    :root {
        --bps-blue: #0058a8;
        --grid-border: #f1f5f9;
        --weekend-bg: #fff5f5;
    }

    .monitoring-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); background: white; }
    .table-responsive { border-radius: 15px; overflow: auto; max-height: 700px; }
    .table thead th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 12px 8px; vertical-align: middle; z-index: 100; }
    
    .sticky-name-col { 
        position: sticky; left: 0; background: white !important; z-index: 50; 
        min-width: 260px; border-right: 2px solid #e2e8f0 !important;
        box-shadow: 5px 0 10px rgba(0,0,0,0.02);
    }

    .day-cell { min-width: 50px; height: 65px; padding: 0 !important; position: relative; border-right: 1px solid var(--grid-border); }
    .weekend-cell { background-color: var(--weekend-bg) !important; }
    .today-cell { background-color: rgba(0, 88, 168, 0.05) !important; border-bottom: 2px solid var(--bps-blue) !important; }

    .agenda-pill {
        position: absolute; top: 15%; left: 5%; right: 5%; bottom: 15%;
        border-radius: 10px; cursor: pointer; display: flex; align-items: center;
        justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 5; color: white;
    }
    .agenda-pill:hover { transform: scale(1.1); z-index: 10; box-shadow: 0 8px 15px rgba(0,0,0,0.15); }

    .pill-tugas { background: linear-gradient(135deg, #0058a8, #007bff); }
    .pill-rapat { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .pill-selesai { background: linear-gradient(135deg, #10b981, #059669) !important; }

    .view-filter-btn { border-radius: 10px; font-weight: 700; font-size: 0.75rem; padding: 8px 16px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; }
    .view-filter-btn.active { background: var(--bps-blue); color: #fff; border-color: var(--bps-blue); }
</style>

<div class="container-fluid px-4">
    <div class="card monitoring-card shadow-sm mb-4">
        <div class="card-body p-4">
            
            {{-- Header Timeline --}}
            <div class="row align-items-center mb-4">
                <div class="col-xl-4 col-lg-12 mb-3 mb-xl-0">
                    <div class="d-flex align-items-center mb-1">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Timeline Monitoring</h4>
                    </div>
                    <p class="text-muted small mb-0">Visualisasi beban kerja personil BPS Tuban.</p>
                </div>
                
                <div class="col-xl-8 col-lg-12">
                    <div class="d-flex flex-wrap justify-content-xl-end gap-3 align-items-center">
                        {{-- Toggle Mode Mingguan / Bulanan --}}
                        <div class="btn-group shadow-sm p-1 bg-light rounded-3">
                            <a href="{{ route('monitoring.index', ['view_mode' => 'week', 'month' => $month, 'year' => $year]) }}" 
                               class="btn view-filter-btn {{ $viewMode == 'week' ? 'active' : '' }}">Mingguan</a>
                            <a href="{{ route('monitoring.index', ['view_mode' => 'month', 'month' => $month, 'year' => $year]) }}" 
                               class="btn view-filter-btn {{ $viewMode == 'month' ? 'active' : '' }}">Bulanan</a>
                        </div>

                        {{-- Legend Warna --}}
                        <div class="d-flex gap-3 px-3 border-end border-start d-none d-sm-flex">
                            <div class="legend-item"><div class="legend-color pill-tugas" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Tugas</small></div>
                            <div class="legend-item"><div class="legend-color pill-rapat" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Rapat</small></div>
                            <div class="legend-item"><div class="legend-color pill-selesai" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Selesai</small></div>
                        </div>

                        {{-- Form Filter --}}
                        <form action="{{ route('monitoring.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                            <div class="input-group input-group-sm shadow-sm">
                                <select name="month" class="form-select fw-bold border-0 bg-light">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="year" class="form-select fw-bold border-0 bg-light">
                                    @for($y = date('Y')-1; $y <= date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="btn btn-primary px-3"><i class="fas fa-filter"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Table Grid --}}
            <div class="table-responsive scrollbar-custom border shadow-sm rounded-4">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="sticky-name-col py-4 text-center text-uppercase small fw-bold">Petugas Pelaksana</th>
                            @for($i = 0; $i < $daysToShow; $i++)
                                @php 
                                    $dt = $startDate->copy()->addDays($i);
                                    $isWeekend = $dt->isWeekend();
                                    $isToday = $dt->isToday();
                                @endphp
                                <th class="text-center py-3 {{ $isWeekend ? 'weekend-cell text-danger' : 'text-primary' }} {{ $isToday ? 'today-cell' : '' }}" style="min-width: 55px;">
                                    <span class="d-block h6 fw-bold mb-0">{{ $dt->format('d') }}</span>
                                    <small class="fw-bold text-uppercase" style="font-size: 0.55rem;">{{ $dt->translatedFormat('D') }}</small>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="sticky-name-col px-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-init me-3 shadow-sm">
                                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.85rem;">{{ $user->nama_lengkap }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $user->team->nama_tim ?? 'Internal' }}</small>
                                    </div>
                                </div>
                            </td>

                            @for($i = 0; $i < $daysToShow; $i++)
                                @php
                                    $dtCheck = $startDate->copy()->addDays($i);
                                    $currentDateStr = $dtCheck->format('Y-m-d');
                                    
                                    $agenda = $user->agendas->first(function($a) use ($currentDateStr) {
                                        return $currentDateStr >= $a->event_date->format('Y-m-d') && $currentDateStr <= $a->end_date->format('Y-m-d');
                                    });
                                    $isWeekend = $dtCheck->isWeekend();
                                    $isToday = $dtCheck->isToday();
                                @endphp
                                <td class="day-cell {{ $isWeekend ? 'weekend-cell' : '' }} {{ $isToday ? 'today-cell' : '' }}">
                                    @if($agenda)
                                        @php
                                            $pillClass = $agenda->status_laporan == 'Selesai' ? 'pill-selesai' : ($agenda->activity_type_id == 2 ? 'pill-rapat' : 'pill-tugas');
                                            $icon = $agenda->status_laporan == 'Selesai' ? 'fa-check' : ($agenda->activity_type_id == 2 ? 'fa-users' : 'fa-briefcase');
                                            $labelTipe = $agenda->activity_type_id == 2 ? "Rapat" : "Tugas";
                                            $namaTim = $agenda->creator && $agenda->creator->team ? $agenda->creator->team->nama_tim : "Umum";
                                            $asalPenugasan = $labelTipe . " dari " . $namaTim;
                                        @endphp
                                        <div class="agenda-pill {{ $pillClass }}" 
                                             onclick="showDetail('{{ $agenda->title }}', '{{ $agenda->location }}', '{{ $user->nama_lengkap }}', '{{ $agenda->status_laporan }}', '{{ $asalPenugasan }}')">
                                            <i class="fas {{ $icon }} shadow-sm" style="font-size: 0.7rem;"></i>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(title, lokasi, pegawai, status, jenis) {
        Swal.fire({
            title: `<div class="mb-2 small text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing:1px;">Rincian Agenda</div><div class="px-3 text-primary">${title}</div>`,
            html: `
                <div class="text-start border-top pt-3 mx-2">
                    <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                        <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-users-viewfinder text-primary"></i></div>
                        <div><small class="text-muted d-block">Asal Penugasan</small><span class="fw-bold text-dark">${jenis}</span></div>
                    </div>
                    <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                        <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-user-check text-success"></i></div>
                        <div><small class="text-muted d-block">Personil</small><span class="fw-bold text-dark">${pegawai}</span></div>
                    </div>
                    <div class="mb-3 d-flex align-items-center p-2 bg-light rounded-3">
                        <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-map-pin text-danger"></i></div>
                        <div><small class="text-muted d-block">Lokasi/Ruang</small><span class="fw-bold text-dark">${lokasi}</span></div>
                    </div>
                    <div class="mb-0 d-flex align-items-center p-2 bg-light rounded-3">
                        <div class="bg-white p-2 rounded-2 me-3 shadow-sm"><i class="fas fa-info-circle text-info"></i></div>
                        <div><small class="text-muted d-block">Status</small>
                            <span class="badge ${status == 'Selesai' ? 'bg-success' : 'bg-warning text-dark'} border-0 px-3 mt-1 fw-bold shadow-sm">${status}</span>
                        </div>
                    </div>
                </div>
            `,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#0058a8',
            customClass: { popup: 'rounded-4 shadow-lg border-0' }
        });
    }
</script>
@endsection