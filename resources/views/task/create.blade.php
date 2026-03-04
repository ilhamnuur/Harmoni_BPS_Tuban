@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            {{-- Header Form --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-primary p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3">
                            <i class="fas fa-file-signature text-white fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Form Laporan Pengawasan Lapangan</h5>
                            <small class="text-white text-opacity-75">Silakan lengkapi detail hasil pengawasan Anda</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-primary rounded-pill px-3 shadow-sm">ID AGENDA: #{{ $agenda->id }}</span>
                </div>
            </div>

            {{-- Info Rentang Waktu --}}
            <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-calendar-alt me-3 fa-lg"></i>
                <div>
                    <strong>Rentang Waktu Penugasan:</strong> 
                    <span class="badge bg-info text-white mx-1">{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d M Y') }}</span> 
                    s/d 
                    <span class="badge bg-info text-white mx-1">{{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            <form action="{{ route('task.store', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- SISI KIRI: Informasi Terkunci & Tanggal Riil --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted">
                                <i class="fas fa-lock me-2"></i>Informasi Penugasan
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NAMA KEGIATAN</label>
                                <textarea class="form-control rounded-3 border-0 fw-bold bg-white text-dark" rows="2" readonly>{{ $agenda->title }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">TUJUAN / LOKASI</label>
                                <input type="text" class="form-control rounded-3 border-0 bg-white text-dark" value="{{ $agenda->location }}" readonly>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">NOMOR SURAT TUGAS</label>
                                {{-- Menggunakan nama kolom surat_nomor_tugas sesuai info kamu --}}
                                <input type="text" class="form-control rounded-3 border-0 bg-white text-dark fw-bold" 
                                       value="{{ $agenda->nomor_surat_tugas ?? 'Belum Diinput' }}" readonly>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-danger border-4">
                            <h6 class="fw-bold mb-3 text-danger">Waktu Pelaksanaan Riil</h6>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark">TANGGAL PELAKSANAAN *</label>
                                {{-- FIX: Memastikan format Y-m-d agar atribut min/max berfungsi di browser --}}
                                <input type="date" name="tanggal_pelaksanaan" 
                                       class="form-control rounded-3 shadow-sm border-danger border-opacity-25" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       required 
                                       value="{{ old('tanggal_pelaksanaan') }}">
                                <div class="form-text text-danger small mt-2" style="font-size: 0.7rem;">
                                    <i class="fas fa-exclamation-circle me-1"></i> Kalender otomatis terkunci sesuai rentang tugas.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Dokumentasi Foto</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">UPLOAD FOTO (Multiple) *</label>
                                <input type="file" name="fotos[]" class="form-control @error('fotos') is-invalid @enderror" accept="image/*" multiple required>
                                <div class="form-text mt-2" style="font-size: 0.7rem;">
                                    <i class="fas fa-info-circle me-1"></i> Maksimal 6 foto (JPG/PNG).
                                </div>
                                @error('fotos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: Input Laporan --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Detail Hasil Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PETUGAS / RESPONDEN YANG DITEMUI *</label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3" placeholder="Sebutkan nama Responden atau Petugas Lapangan..." required value="{{ old('responden') }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS YANG DILAKUKAN *</label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3" rows="4" placeholder="Jelaskan secara detail apa saja yang Anda lakukan di lapangan..." required>{{ old('aktivitas') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN YANG DITEMUI *</label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3" rows="3" placeholder="Sebutkan kendala atau temuan yang kurang sesuai..." required>{{ old('permasalahan') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success">SOLUSI / LANGKAH ANTISIPATIF *</label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3" rows="3" placeholder="Langkah apa yang diambil untuk menyelesaikan masalah tersebut?" required>{{ old('solusi_antisipasi') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('task.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">
                                    <i class="fas fa-arrow-left me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection