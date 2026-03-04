@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    @php 
        $isEdit = !empty($meeting->notulensi_hasil); 
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
            <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-file-signature' }} fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">{{ $isEdit ? 'Edit Notulensi Rapat' : 'Input Hasil Rapat (Notulensi)' }}</h4>
            <p class="text-muted small mb-0">Silakan {{ $isEdit ? 'perbarui' : 'lengkapi' }} poin pembahasan dan dokumentasi rapat.</p>
        </div>
    </div>

    <form action="{{ $isEdit ? route('meeting.notulensi.update', $meeting->id) : route('meeting.notulensi.store', $meeting->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row">
            {{-- KIRI: FORM NOTULENSI --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                            <h6 class="fw-bold text-dark mb-0">Detail Pembahasan</h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Agenda: {{ $meeting->title }}</span>
                        </div>
                        
                        {{-- SEKSI PIMPINAN --}}
                        <div class="mb-4">
                            <label class="small fw-bold mb-2 text-dark">Pimpinan Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                    <i class="fas fa-user-tie text-muted"></i>
                                </span>
                                <input type="text" class="form-control bg-light border-start-0 fw-bold rounded-end-3" 
                                    value="{{ $meeting->creator->nama_lengkap ?? 'Admin' }}" readonly>
                            </div>
                        </div>

                        {{-- SEKSI HASIL RAPAT --}}
                        <div class="mb-3">
                            <label class="small fw-bold mb-2 text-primary">Isi Notulensi / Poin Pembahasan</label>
                            <textarea name="hasil_rapat" class="form-control rounded-4 @error('hasil_rapat') is-invalid @enderror" rows="18" 
                                placeholder="Tuliskan hasil diskusi, keputusan rapat, dan rencana tindak lanjut di sini..." 
                                required style="resize: none; line-height: 1.6;">{{ old('hasil_rapat', $meeting->notulensi_hasil) }}</textarea>
                            @error('hasil_rapat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: DOKUMENTASI & PESERTA --}}
            <div class="col-lg-4">
                {{-- CARD UPLOAD FOTO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-dark">
                        <h6 class="fw-bold mb-3">Dokumentasi Rapat</h6>
                        
                        {{-- Foto Lama (Jika Mode Edit) --}}
                        @if($isEdit && $meeting->dokumentasi_path)
                            <div class="mb-3 p-2 bg-light rounded-3 border">
                                <label class="small text-muted d-block mb-2 fw-bold">Foto Tersimpan:</label>
                                <div class="row g-2">
                                    @php $photos = json_decode($meeting->dokumentasi_path, true) ?? []; @endphp
                                    @foreach($photos as $photo)
                                        <div class="col-4">
                                            <img src="{{ asset('storage/' . $photo) }}" class="img-fluid rounded-2 border shadow-xs" style="height: 60px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-danger mt-2 d-block" style="font-size: 0.6rem;">* Upload foto baru akan mengganti semua foto lama.</small>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="small fw-bold mb-2 text-danger">Unggah Foto Baru {{ $isEdit ? '(Opsional)' : '(Wajib)' }}</label>
                            <input type="file" name="foto_dokumentasi[]" id="foto_dokumentasi" class="form-control rounded-3" 
                                accept="image/*" multiple {{ $isEdit ? '' : 'required' }}>
                        </div>
                        {{-- Container Preview --}}
                        <div id="image-preview-container" class="row g-2 mt-2"></div>
                    </div>
                </div>

                {{-- CARD DAFTAR HADIR --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-dark">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 small text-uppercase" style="letter-spacing: 1px;">Konfirmasi Kehadiran</h6>
                            <span class="badge bg-success rounded-pill px-2" style="font-size: 0.6rem;">
                                {{ count($userSudahHadir) }} Hadir
                            </span>
                        </div>
                        
                        <div class="list-group list-group-flush overflow-auto pe-1" style="max-height: 250px;">
                            @foreach($semuaPeserta as $p)
                                @php $isHadir = in_array($p->assigned_to, $userSudahHadir); @endphp
                                <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center {{ $isHadir ? '' : 'opacity-50' }}">
                                    <div class="avatar-mini {{ $isHadir ? 'bg-success text-white' : 'bg-light text-muted border' }} rounded-circle me-2">
                                        {{ strtoupper(substr($p->assignee->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="small flex-grow-1 text-truncate pe-2">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.75rem;">{{ $p->assignee->nama_lengkap }}</div>
                                        <small class="text-muted" style="font-size: 0.6rem;">{{ $isHadir ? 'Terverifikasi' : 'Tidak Hadir' }}</small>
                                    </div>
                                    <i class="fas {{ $isHadir ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }}" style="font-size: 0.8rem;"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow btn-hover-effect border-0">
                        <i class="fas {{ $isEdit ? 'fa-save' : 'fa-check-double' }} me-2"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Selesaikan Notulensi' }}
                    </button>
                    <a href="{{ route('meeting.history') }}" class="btn btn-link w-100 text-muted mt-2 small text-decoration-none">Batal dan Kembali</a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .avatar-mini { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: bold; }
    .preview-img-wrapper { position: relative; height: 80px; width: 100%; overflow: hidden; border-radius: 10px; border: 2px solid #f1f5f9; }
    .preview-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .btn-primary { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); transition: all 0.3s ease; }
    .btn-hover-effect:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 88, 168, 0.2) !important; filter: brightness(1.1); }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    /* Scrollbar Tipis */
    .list-group::-webkit-scrollbar { width: 3px; }
    .list-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
    function previewImages() {
        const previewContainer = document.querySelector('#image-preview-container');
        previewContainer.innerHTML = ''; 
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `
                        <div class="preview-img-wrapper shadow-sm">
                            <img src="${e.target.result}">
                        </div>
                    `;
                    previewContainer.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    document.querySelector('#foto_dokumentasi').addEventListener('change', previewImages);
</script>
@endsection