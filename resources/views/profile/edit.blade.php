@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Sederhana --}}
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Pengaturan Profil</h4>
        <p class="text-muted small">Kelola informasi data diri dan peran akses Anda.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- SISI KIRI: DATA DIRI --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-user-circle me-2 text-primary"></i>Informasi Akun
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control rounded-3 bg-light border-0 @error('nama_lengkap') is-invalid @enderror" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="small fw-bold mb-1">Username</label>
                                    <input type="text" name="username" class="form-control rounded-3 bg-light border-0 @error('username') is-invalid @enderror" 
                                           value="{{ old('username', $user->username) }}" required>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <h6 class="fw-bold text-dark mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-key me-2 text-warning"></i>Keamanan
                                </h6>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Password Baru</label>
                                    <input type="password" name="password" class="form-control rounded-3 bg-light border-0 @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ganti">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-3 bg-light border-0" placeholder="Ulangi password baru">
                                </div>
                            </div>

                            {{-- SISI KANAN: ROLE & TIM --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-shield-alt me-2 text-success"></i>Akses & Tim
                                </h6>

                                <div class="mb-4 p-3 bg-primary bg-opacity-10 rounded-4">
                                    <label class="small fw-bold d-block mb-1 text-primary">Unit Kerja / Tim saat ini:</label>
                                    <span class="h6 fw-bold mb-0 text-dark">{{ $user->team->nama_tim ?? 'Lintas Tim' }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="small fw-bold mb-3 text-dark text-uppercase" style="letter-spacing: 1px;">Pilih Peran Anda</label>
                                    
                                    @if($user->role != 'Admin')
                                        {{-- Pegawai Option --}}
                                        <div class="role-selection mb-3">
                                            <input type="radio" name="role" id="rolePegawai" value="Pegawai" class="btn-check" {{ $user->role == 'Pegawai' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-light text-start p-3 w-100 rounded-4 shadow-xs border" for="rolePegawai">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">Pegawai Biasa</div>
                                                        <small class="text-muted small">Akses laporan & tugas pribadi</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        {{-- Katim Option --}}
                                        <div class="role-selection">
                                            <input type="radio" name="role" id="roleKatim" value="Katim" class="btn-check" {{ $user->role == 'Katim' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-light text-start p-3 w-100 rounded-4 shadow-xs border" for="roleKatim">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">Ketua Tim (Katim)</div>
                                                        <small class="text-muted small">Akses plotting & monitoring tim</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @else
                                        <div class="p-3 border rounded-4 bg-light">
                                            <div class="fw-bold text-primary"><i class="fas fa-star me-2"></i>Administrator</div>
                                            <small class="text-muted">Akses penuh sistem tidak dapat diubah sendiri.</small>
                                            <input type="hidden" name="role" value="Admin">
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Radio Button Custom agar tidak membingungkan */
    .btn-check:checked + .btn-outline-light {
        background-color: #fff !important;
        border-color: #0058a8 !important;
        box-shadow: 0 4px 12px rgba(0, 88, 168, 0.15) !important;
    }
    .btn-check:checked + .btn-outline-light .fw-bold {
        color: #0058a8 !important;
    }
    .btn-outline-light {
        border-color: #f1f5f9 !important;
        transition: all 0.2s ease;
    }
    .btn-outline-light:hover {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(0, 88, 168, 0.1) !important;
    }
</style>
@endsection