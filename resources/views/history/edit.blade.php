@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            {{-- Header Form --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-warning p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-edit fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Perbarui Laporan Pengawasan</h5>
                            <small class="text-white text-opacity-75">Update hasil temuan lapangan untuk ID Agenda: #{{ $agenda->id }}</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-warning rounded-pill px-3 shadow-sm fw-bold">MODE EDIT</span>
                </div>
            </div>

            <form action="{{ route('history.update', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                    {{-- SISI KIRI: Informasi Baku & Tanggal --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted">
                                <i class="fas fa-lock me-2"></i>Informasi Baku
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan</label>
                                <textarea class="form-control rounded-3 border-0 fw-bold bg-white text-dark" rows="2" readonly>{{ $agenda->title }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor Surat Tugas</label>
                                <input type="text" class="form-control rounded-3 border-0 bg-white text-dark fw-bold" 
                                       value="{{ $agenda->nomor_surat_tugas ?? 'Belum Diatur' }}" readonly>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Lokasi Penugasan</label>
                                <input type="text" class="form-control rounded-3 border-0 bg-white text-dark" value="{{ $agenda->location }}" readonly>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-warning border-4">
                            <h6 class="fw-bold mb-3 text-warning">Waktu Pelaksanaan</h6>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark">TANGGAL PELAKSANAAN RIIL *</label>
                                <input type="date" name="tanggal_pelaksanaan" 
                                       class="form-control rounded-3 shadow-sm" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       required 
                                       value="{{ old('tanggal_pelaksanaan', $agenda->tanggal_pelaksanaan) }}">
                                <div class="form-text text-muted small mt-2" style="font-size: 0.7rem;">
                                    <i class="fas fa-info-circle me-1"></i> Rentang: {{ date('d M', strtotime($agenda->event_date)) }} s/d {{ date('d M Y', strtotime($agenda->end_date)) }}
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Dokumentasi Foto</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">TAMBAH FOTO BARU</label>
                                <input type="file" name="fotos[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">* Biarkan kosong jika tidak ingin menambah foto.</small>
                            </div>
                            
                            {{-- Preview Foto yang Sudah Ada --}}
                            <div class="mt-3">
                                <label class="form-label small fw-bold d-block mb-2">FOTO SAAT INI:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($agenda->photos as $photo)
                                        <div class="position-relative border rounded-3 overflow-hidden shadow-sm" style="width: 60px; height: 60px;">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: Detail Laporan --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-primary">Isi Laporan Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PETUGAS / RESPONDEN YANG DITEMUI *</label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3" 
                                       placeholder="Nama petugas lapangan atau responden..." 
                                       required value="{{ old('responden', $agenda->responden) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS YANG DILAKUKAN *</label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3" rows="4" 
                                          placeholder="Apa saja yang dilakukan di lokasi?" 
                                          required>{{ old('aktivitas', $agenda->aktivitas) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN YANG DITEMUI *</label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3" rows="3" 
                                          placeholder="Temuan kendala di lapangan..." 
                                          required>{{ old('permasalahan', $agenda->permasalahan) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success">SOLUSI / LANGKAH ANTISIPATIF *</label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3" rows="3" 
                                          placeholder="Tindakan yang diambil..." 
                                          required>{{ old('solusi_antisipasi', $agenda->solusi_antisipasi) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('history.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">
                                    <i class="fas fa-arrow-left me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning px-5 rounded-pill fw-bold shadow text-white">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
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