@php
    \Carbon\Carbon::setLocale('id');
@endphp

@extends('layouts.app')

@section('content')
<style>
    /* Dashboard Specific Styles */
    :root {
        --bps-blue: #0058a8;
        --bps-dark-blue: #003d75;
        --bps-gold: #ffc107;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--bps-blue) 0%, #007bff 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 20px rgba(0, 88, 168, 0.15);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::after {
        content: "";
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .stat-card {
        border: none;
        border-radius: 18px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 1.5rem;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .table thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        font-weight: 700;
        color: #64748b;
        border: none;
        padding: 1.25rem 1rem;
    }

    .table tbody td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .badge-pill-custom {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .avatar-circle {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        color: var(--bps-blue);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .letter-spacing-1 { letter-spacing: 1px; }

    /* Animasi Berkedip untuk Tugas Mendesak */
    .animate-pulse-red {
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    .urgent-row {
        background-color: rgba(255, 193, 7, 0.05) !important;
    }
</style>

<div class="container-fluid px-4">
    {{-- Header Dashboard --}}
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 text-white">Halo, {{ $nama }} 👋</h2>
            <p class="opacity-75 mb-0 text-uppercase small letter-spacing-1 fw-medium text-white">
                <i class="fas fa-id-badge me-2"></i>{{ $role }} &bull; {{ $tim }}
            </p>
        </div>
        <div class="text-end d-none d-md-block text-white">
            <div class="h5 fw-bold mb-0" style="color: #ffda6a;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
            <small class="opacity-75">BPS Kabupaten Tuban</small>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row g-4 mb-4">
        @if($role == 'Pegawai')
            <div class="col-md-6">
                <div class="card stat-card shadow-sm h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fas fa-hourglass-start"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Tugas Pending</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $tugas_pending }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card shadow-sm h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Laporan Selesai</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $tugas_selesai }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">
                                {{ $role == 'Admin' ? 'Total Seluruh User' : 'Anggota Tim' }}
                            </p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $total_pegawai }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Total Agenda</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $total_agenda }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Selesai Lapor</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $tugas_selesai }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tabel Agenda Terkini --}}
    <div class="card table-container shadow-sm overflow-hidden border-0">
        <div class="card-header bg-white border-0 px-4 py-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Agenda & Tugas Terkini</h5>
                <small class="text-muted">Prioritas: Tugas Pending & Deadline Terdekat</small>
            </div>
            <a href="{{ route('agenda.all') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Kegiatan & Lokasi</th>
                        <th>Petugas Pelaksana</th>
                        <th class="text-center">Jadwal Pelaksanaan</th>
                        <th class="text-center">Status Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agenda_terbaru as $agenda)
                    @php
                        $isPending = $agenda->status_laporan == 'Pending';
                        $eventDate = \Carbon\Carbon::parse($agenda->event_date);
                        // Urgent jika pending dan jadwalnya H-0 atau H-1
                        $isUrgent = $isPending && ($eventDate->isToday() || $eventDate->isTomorrow());
                    @endphp
                    <tr class="{{ $isUrgent ? 'urgent-row' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($isUrgent)
                                    <span class="badge bg-danger p-1 rounded-circle me-2 animate-pulse-red" style="width: 10px; height: 10px;" title="Sangat Mendesak!"></span>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark mb-0">{{ $agenda->title }}</div>
                                    <small class="text-muted fw-medium">
                                        <i class="fas fa-map-marker-alt me-1 small text-danger"></i>{{ $agenda->location }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($agenda->assignee->nama_lengkap ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.85rem;">
                                        {{ $agenda->assignee->nama_lengkap ?? 'Tanpa Nama' }}
                                        @if($agenda->assigned_to == auth()->id())
                                            <span class="badge bg-primary ms-1" style="font-size: 0.5rem; vertical-align: middle;">ANDA</span>
                                        @endif
                                    </div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 700;">{{ $agenda->assignee->role ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $dateClass = $isUrgent ? 'border-danger text-danger' : 'border-light text-dark';
                            @endphp
                            <div class="badge bg-white {{ $dateClass }} fw-bold px-3 py-2 border shadow-sm" style="font-size: 0.75rem;">
                                <i class="far fa-calendar-alt me-2 {{ $isUrgent ? 'text-danger' : 'text-primary' }}"></i>
                                {{ $eventDate->translatedFormat('d M') }} 
                                <span class="mx-1 text-muted">-</span> 
                                {{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d M Y') }}
                            </div>
                            @if($isUrgent)
                                <div class="text-danger fw-bold mt-1" style="font-size: 0.6rem; text-transform: uppercase;">Mendesak!</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!$isPending)
                                <span class="badge badge-pill-custom bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                                    <i class="fas fa-check-circle me-1"></i> Terkirim
                                </span>
                            @else
                                <span class="badge badge-pill-custom bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 shadow-sm">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Tidak ada agenda aktif untuk ditampilkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="small text-muted fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $agenda_terbaru->firstItem() ?? 0 }}</span> 
                    sampai <span class="text-dark fw-bold">{{ $agenda_terbaru->lastItem() ?? 0 }}</span> 
                    dari <span class="text-dark fw-bold">{{ $agenda_terbaru->total() }}</span> agenda
                </div>
                <div class="pagination-custom">
                    {{ $agenda_terbaru->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Pagination supaya mungil & profesional */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    .pagination .page-link {
        border-radius: 10px !important;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--bps-blue);
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--bps-blue) 0%, #007bff 100%);
        border-color: transparent;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        color: #cbd5e1;
    }
</style>
@endsection