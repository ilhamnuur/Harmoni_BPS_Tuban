@php
    \Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')

@section('content')
<style>
    /* 1. Table Core Styling */
    .table-history {
        background: white;
        border-radius: 0 0 20px 20px;
    }

    .table-history thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        font-weight: 800;
        padding: 1.1rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
    }

    .table-history tbody td {
        padding: 1rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* 2. Compact Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .btn-view { background: #e0f2fe; color: #0369a1; }
    .btn-edit { background: #fef9c3; color: #a16207; }
    .btn-pdf { background: #fef2f2; color: #b91c1c; }
    .btn-delete { background: #fff1f0; color: #cf1322; }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }

    /* 3. Header Action Bar */
    .action-header-card {
        background: white;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.5rem;
    }

    .btn-rekap {
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        color: white !important;
    }

    .btn-rekap-pdf { background: linear-gradient(135deg, #ff4d4f 0%, #cf1322 100%); }
    .btn-rekap-excel { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
    
    .btn-rekap:hover {
        filter: brightness(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .search-group-custom {
        max-width: 350px;
        width: 100%;
    }

    .search-group-custom .form-control {
        border-radius: 10px 0 0 10px !important;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.85rem;
    }

    .search-group-custom .btn {
        border-radius: 0 10px 10px 0 !important;
        border: 1px solid #e2e8f0;
        border-left: none;
        background: #f8fafc;
        color: #64748b;
    }

    /* 4. Data Components */
    .date-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .location-info {
        color: #64748b;
        font-size: 0.8rem;
        display: block;
        margin-top: 2px;
    }
</style>

<div class="container-fluid px-4">
    {{-- Page Info --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1 text-dark">Riwayat Laporan</h4>
        <p class="text-muted small">Kelola dan monitor seluruh laporan aktivitas yang telah selesai.</p>
    </div>

    {{-- Main Wrapper --}}
    <div class="card border-0 shadow-sm rounded-4">
        {{-- Header Bar --}}
        <div class="action-header-card">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Katim')
                            <a href="{{ route('history.pdf_rekap') }}" class="btn-rekap btn-rekap-pdf shadow-sm">
                                <i class="fas fa-file-pdf"></i> PDF Rekap
                            </a>
                            <a href="{{ route('history.excel_rekap') }}" class="btn-rekap btn-rekap-excel shadow-sm">
                                <i class="fas fa-file-excel"></i> Excel Rekap
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end">
                    <form action="{{ route('history.index') }}" method="GET" class="search-group-custom">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari petugas atau kegiatan..." value="{{ request('search') }}">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-history mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" width="25%">Petugas Pelaksana</th>
                            <th width="35%">Detail Kegiatan</th>
                            <th width="15%" class="text-center">Tanggal</th>
                            <th class="text-center pe-4" width="20%">Aksi Manajerial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box me-3 bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 0.8rem; border: 1px solid rgba(0,88,168,0.1);">
                                        {{ strtoupper(substr($r->assignee->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1">{{ $r->assignee->nama_lengkap }}</div>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $r->creator->team->nama_tim ?? 'Lintas Tim' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0 lh-sm">{{ $r->title }}</div>
                                <span class="location-info">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $r->location }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="date-badge">
                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($r->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('history.detail', $r->id) }}" class="btn-action btn-view" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('history.edit', $r->id) }}" class="btn-action btn-edit" title="Ubah"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('history.export', $r->id) }}" class="btn-action btn-pdf" title="Cetak PDF"><i class="fas fa-file-pdf"></i></a>
                                    
                                    @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Katim')
                                    <form action="{{ route('assignment.destroy', $r->id) }}" method="POST" id="form-delete-{{ $r->id }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $r->id }}, '{{ $r->title }}')" class="btn-action btn-delete" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                <p class="text-muted fw-bold">Tidak ada riwayat laporan ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Hapus Laporan?',
            html: `Anda akan menghapus laporan <strong>"${title}"</strong> secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }
</script>
@endsection