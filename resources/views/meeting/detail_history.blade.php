@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header & Navigasi --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success shadow-sm border border-success border-opacity-10">
                <i class="fas fa-file-contract fa-lg"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Detail Riwayat Rapat</h4>
                <p class="text-muted small mb-0 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Arsip Digital Notulensi</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meeting.history') }}" class="btn btn-white rounded-pill px-4 fw-bold shadow-sm border text-muted">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('meeting.print_presensi', $meeting->id) }}" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm px-4">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
        </div>
    </div>

    {{-- BARIS 1: INFO UTAMA & MATERI --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 border-end-lg">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-bold" style="font-size: 0.6rem;">NAMA KEGIATAN</span>
                    <h4 class="fw-bold text-dark mb-3">{{ $meeting->title }}</h4>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-light border shadow-xs" style="flex: 1;">
                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.6rem; font-weight: 700;">Pimpinan</small>
                                <span class="fw-bold text-dark small">{{ $meeting->creator->nama_lengkap ?? 'Admin' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2 rounded-3 bg-light border shadow-xs" style="flex: 1;">
                            <div class="bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.6rem; font-weight: 700;">Notulis</small>
                                <span class="fw-bold text-dark small">{{ $meeting->notulis->nama_lengkap ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-4">
                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">Lampiran Materi</h6>
                    @if($meeting->materi_path)
                        <div class="d-flex align-items-center p-2 rounded-4 border bg-white shadow-xs">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <div class="fw-bold text-dark mb-0 small text-truncate">Dokumen Materi Rapat</div>
                                <a href="{{ asset('storage/' . $meeting->materi_path) }}" target="_blank" class="text-primary small text-decoration-none fw-bold">
                                    <i class="fas fa-download me-1"></i> Klik untuk Unduh
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-4 border bg-light text-center border-dashed">
                            <small class="text-muted italic small">Tidak ada file materi.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: NOTULENSI vs DOKUMENTASI & PESERTA --}}
    <div class="row g-4">
        {{-- SISI KIRI: HASIL PEMBAHASAN --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-book-open text-primary me-2"></i>Hasil Pembahasan
                    </h6>
                    <div class="notulensi-container p-3 rounded-4 bg-light bg-opacity-50 border shadow-inner">
                        @if($meeting->notulensi_hasil)
                            <div class="notulensi-text">{{ trim($meeting->notulensi_hasil) }}</div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-comment-slash fa-2x mb-3 opacity-25"></i>
                                <p class="small">Belum ada catatan hasil rapat.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: GALLERY FOTO & PESERTA --}}
        <div class="col-lg-5">
            <div class="row g-4">
                {{-- MULTIPLE FOTO DOKUMENTASI --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-dark">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Dokumentasi Kegiatan</h6>
                            
                            @php 
                                $photos = json_decode($meeting->dokumentasi_path, true) ?? []; 
                            @endphp

                            @if(count($photos) > 0)
                                <div class="row g-2">
                                    @foreach($photos as $index => $photo)
                                        <div class="col-6 col-md-4">
                                            <div class="position-relative img-container rounded-3 overflow-hidden shadow-sm border border-light">
                                                <img src="{{ asset('storage/' . $photo) }}" 
                                                     class="img-fluid w-100" 
                                                     style="height: 100px; object-fit: cover;"
                                                     alt="Foto Dokumentasi">
                                                <div class="img-overlay d-flex flex-column align-items-center justify-content-center gap-2">
                                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Lihat Foto">
                                                        <i class="fas fa-search-plus text-primary"></i>
                                                    </a>
                                                    <a href="{{ asset('storage/' . $photo) }}" download="Dokumentasi_{{ $index + 1 }}_{{ Str::slug($meeting->title) }}" class="btn btn-sm btn-primary rounded-circle shadow-sm" title="Unduh Foto">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted italic" style="font-size: 0.65rem;">* Arahkan kursor ke foto untuk melihat/mengunduh.</small>
                                </div>
                            @else
                                <div class="py-4 bg-light rounded-3 border border-dashed text-center">
                                    <i class="fas fa-images text-muted opacity-25 fa-2x"></i>
                                    <p class="small text-muted mb-0 mt-2">Belum ada foto dokumentasi.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Partisipan --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-dark">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Daftar Hadir Peserta</h6>
                            <div class="list-group list-group-flush overflow-auto pe-1" style="max-height: 300px;">
                                @foreach($semuaPeserta as $p)
                                    @php $isHadir = in_array($p->assigned_to, $userSudahHadir); @endphp
                                    <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center {{ $isHadir ? '' : 'opacity-50' }}">
                                        <div class="avatar-circle {{ $isHadir ? 'bg-success text-white' : 'bg-light text-muted border' }} rounded-circle me-2">
                                            {{ strtoupper(substr($p->assignee->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="small flex-grow-1 text-truncate pe-1">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.75rem;">{{ $p->assignee->nama_lengkap }}</div>
                                            <div class="text-muted" style="font-size: 0.6rem;">{{ $p->assignee->team->nama_tim ?? 'BPS Kabupaten' }}</div>
                                        </div>
                                        <i class="fas {{ $isHadir ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }}" style="font-size: 0.8rem;"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (min-width: 992px) {
        .border-end-lg { border-right: 1px solid #dee2e6 !important; }
    }
    .notulensi-text {
        white-space: pre-wrap; line-height: 1.6; color: #334155;
        font-size: 0.95rem; text-align: justify; word-break: break-word;
    }
    .avatar-circle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; }
    
    .img-container { transition: all 0.3s ease; }
    .img-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 88, 168, 0.6); opacity: 0; transition: all 0.3s ease;
        backdrop-filter: blur(2px);
    }
    .img-container:hover .img-overlay { opacity: 1; }
    .img-overlay .btn { transform: translateY(10px); transition: all 0.3s ease; }
    .img-container:hover .img-overlay .btn { transform: translateY(0); }

    .list-group::-webkit-scrollbar { width: 3px; }
    .list-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
</style>
@endsection