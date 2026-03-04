@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Riwayat & Notulensi</h4>
            <p class="text-muted small mb-0">Manajemen arsip dokumentasi dan hasil rapat yang telah terlaksana.</p>
        </div>
        <div class="bg-primary bg-opacity-10 p-2 px-3 rounded-4 border border-primary border-opacity-10">
            <i class="fas fa-archive text-primary me-2"></i>
            <span class="fw-bold small text-primary">{{ $historyMeetings->count() }} Agenda Tersimpan</span>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('meeting.history') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari judul rapat atau lokasi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold"> Cari</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat Modern --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4" style="width: 15%;">Tanggal</th>
                        <th class="border-0 py-3" style="width: 35%;">Agenda Rapat</th>
                        <th class="border-0 py-3">Notulis</th>
                        <th class="border-0 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyMeetings as $meeting)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($meeting->event_date)->translatedFormat('d M Y') }}</div>
                            <small class="text-muted">{{ $meeting->start_time ?? '--:--' }} WIB</small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary mb-1">{{ $meeting->title }}</div>
                            <div class="small text-muted text-truncate" style="max-width: 250px;">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $meeting->location }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm-table bg-info text-white me-2">
                                    {{ substr($meeting->notulis->nama_lengkap ?? 'N', 0, 1) }}
                                </div>
                                <div class="small fw-bold text-dark">{{ $meeting->notulis->nama_lengkap ?? '-' }}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Button Lihat Detail --}}
                                <a href="{{ route('meeting.history.detail', $meeting->id) }}" class="btn btn-light btn-sm rounded-3 shadow-xs" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>

                                {{-- Akses Khusus Notulis atau Admin --}}
                                @if($meeting->notulis_id == Auth::id() || Auth::user()->role == 'Admin')
                                    <a href="{{ route('meeting.notulensi', $meeting->id) }}" class="btn btn-light btn-sm rounded-3 shadow-xs" title="Edit Notulensi">
                                        <i class="fas fa-edit text-warning"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-light btn-sm rounded-3 shadow-xs btn-delete" 
                                            data-id="{{ $meeting->id }}" 
                                            data-title="{{ $meeting->title }}"
                                            title="Hapus Riwayat">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                    
                                    {{-- Form Hapus (Tersembunyi) --}}
                                    <form id="delete-form-{{ $meeting->id }}" action="{{ route('meeting.history.destroy', $meeting->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/amber/empty-folder.svg" style="height: 120px;" class="mb-3 opacity-50">
                            <h6 class="fw-bold text-muted">Belum ada riwayat rapat yang tersimpan.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .avatar-sm-table {
        width: 28px; height: 28px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 800;
    }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .table thead th { font-size: 0.7rem; letter-spacing: 0.5px; }
    .btn-light:hover { background: #fff; border-color: var(--bps-blue); }
    tr { transition: all 0.2s; }
    tr:hover { background-color: #f8fafc; }
</style>

{{-- Script Hapus dengan SweetAlert2 --}}
<script>
    $('.btn-delete').click(function() {
        let id = $(this).data('id');
        let title = $(this).data('title');
        
        Swal.fire({
            title: 'Hapus Riwayat?',
            text: "Data notulensi '" + title + "' akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });
</script>
@endsection