@php
    \Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Daftar Laporan Pengawasan</h4>
            </div>

            <form action="{{ route('manajemen.laporan') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari pegawai/kegiatan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7 text-end">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary px-3 rounded-start-pill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('manajemen.laporan') }}" class="btn btn-light border px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>

                    <a href="{{ route('manajemen.laporan.cetak', request()->all()) }}" class="btn btn-danger rounded-pill px-3 ms-2">
                        <i class="fas fa-print me-1"></i> Cetak Rekap (PDF)
                    </a>

                    <a href="{{ route('manajemen.laporan.excel', request()->all()) }}" class="btn btn-success rounded-pill px-3 ms-1">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Tujuan Pengawasan</th>
                            <th>No. Surat Tugas</th>
                            <th class="text-center">Hari & Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $l)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $l->assignee->nama_lengkap }}</div>
                                <small class="text-muted">{{ $l->assignee->role }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $l->title }}</div>
                                <small class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $l->location }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">{{ $l->nomor_surat_tugas ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="small fw-bold">{{ \Carbon\Carbon::parse($l->event_date)->translatedFormat('l') }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($l->event_date)->translatedFormat('d/m/Y') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
                                    <i class="fas fa-check-circle me-1"></i> Selesai
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('riwayat.export', $l->id) }}" class="btn btn-sm btn-outline-danger rounded-circle" title="Cetak Dokumen Satuan">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('riwayat.detail', $l->id) }}" class="btn btn-sm btn-outline-info rounded-circle" title="Detail Laporan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-light mb-3 d-block"></i>
                                <span class="text-muted">Tidak ada laporan pengawasan yang ditemukan.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-outline-danger:hover, .btn-outline-info:hover { color: white !important; }
</style>
@endsection