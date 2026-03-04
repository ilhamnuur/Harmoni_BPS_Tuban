@php
    \Carbon\Carbon::setLocale('id');
@endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('history.index') }}" class="text-decoration-none">Riwayat</a></li>
                    <li class="breadcrumb-item active">Detail Laporan</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Detail Laporan Perjalanan Dinas</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('history.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('history.export', $agenda->id) }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-file-pdf me-2"></i>Cetak PDF
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-user-tie text-primary fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $agenda->assignee->nama_lengkap }}</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $agenda->assignee->role }}</span>
                </div>
                
                <hr class="opacity-25">

                <div class="mb-3">
                    <label class="small text-muted d-block fw-bold text-uppercase mb-1">Nomor Surat Tugas</label>
                    <p class="text-dark fw-semibold mb-0">{{ $agenda->nomor_surat_tugas ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block fw-bold text-uppercase mb-1">Tujuan Perjalanan Dinas</label>
                    <p class="text-dark fw-semibold mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $agenda->location }}</p>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small text-muted d-block fw-bold text-uppercase mb-1">Tanggal</label>
                        <p class="text-dark fw-semibold mb-0">{{ \Carbon\Carbon::parse($agenda->event_date)->format('d F Y') }}</p>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small text-muted d-block fw-bold text-uppercase mb-1">Hari</label>
                        <p class="text-dark fw-semibold mb-0">{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l') }}</p>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="small text-muted d-block fw-bold text-uppercase mb-1">Petugas/Responden Ditemui</label>
                    <p class="text-dark fw-semibold mb-0">{{ $agenda->responden ?? '-' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-camera me-2 text-primary"></i>Foto Dokumentasi</h6>
                <div class="row g-2">
                    @forelse($agenda->photos as $photo)
                    <div class="col-6">
                        <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" class="img-fluid rounded-3 shadow-sm border" style="height: 100px; width: 100%; object-fit: cover;">
                        </a>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted small text-center py-3 bg-light rounded-3">Tidak ada foto dokumentasi</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 h-100">
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-walking"></i>
                        </div>
                        <h5 class="fw-bold mb-0">I. Aktivitas yang Dilakukan</h5>
                    </div>
                    <div class="text-dark lh-lg text-justify bg-light p-4 rounded-4" style="white-space: pre-line;">
                        {!! nl2br(e($agenda->aktivitas)) !!}
                    </div>
                </div>

                <div class="mb-5">
                    <div class="d-flex align-items-center mb-3 text-danger">
                        <div class="bg-danger p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-danger">II. Permasalahan yang Ditemui</h5>
                    </div>
                    <div class="text-dark lh-lg text-justify bg-danger bg-opacity-10 p-4 rounded-4 border-start border-danger border-4" style="white-space: pre-line;">
                        {!! nl2br(e($agenda->permasalahan)) !!}
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex align-items-center mb-3 text-success">
                        <div class="bg-success p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-success">III. Solusi / Langkah Antisipatif</h5>
                    </div>
                    <div class="text-dark lh-lg text-justify bg-success bg-opacity-10 p-4 rounded-4 border-start border-success border-4" style="white-space: pre-line;">
                        {!! nl2br(e($agenda->solusi_antisipasi)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-justify {
        text-align: justify;
    }
    .lh-lg {
        line-height: 1.8;
    }
</style>
@endsection