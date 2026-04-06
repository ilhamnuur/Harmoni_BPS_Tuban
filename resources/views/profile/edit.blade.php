@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Sederhana --}}
    <div class="mb-4 mt-4">
        <h4 class="fw-bold text-dark mb-1">Pengaturan Profil</h4>
        <p class="text-muted small">Kelola informasi data diri dan peran akses Anda dalam sistem Harmoni.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- DITAMBAHKAN enctype UNTUK UPLOAD FILE --}}
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- SISI KIRI: DATA DIRI & KEAMANAN --}}
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

                                {{-- FITUR BARU: UPLOAD TTD DIGITAL (KHUSUS KEPALA/KATIM) --}}
                                @if(in_array($user->role, ['Kepala', 'Katim']))
                                <div class="mb-4 p-3 border rounded-4 bg-white shadow-xs">
                                    <label class="small fw-bold mb-2 d-block text-dark">
                                        <i class="fas fa-pen-nib me-1 text-primary"></i> Tanda Tangan Digital
                                    </label>
                                    
                                    @if($user->signature)
                                        <div class="mb-3 p-2 border rounded bg-light text-center">
                                            <img src="{{ asset('storage/' . $user->signature) }}" alt="TTD Digital" style="max-height: 80px; width: auto;">
                                            <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Tanda tangan aktif saat ini</p>
                                        </div>
                                    @endif

                                    <input type="file" name="signature" class="form-control form-control-sm rounded-3 @error('signature') is-invalid @enderror" accept="image/png">
                                    <div class="form-text text-muted" style="font-size: 0.65rem;">
                                        Format: <b>PNG Transparan</b> (Max 2MB). Digunakan untuk cetak dokumen rapat.
                                    </div>
                                    @error('signature') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @endif

                                <h6 class="fw-bold text-dark mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-key me-2 text-warning"></i>Keamanan
                                </h6>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Password Baru</label>
                                    <input type="password" name="password" class="form-control rounded-3 bg-light border-0 @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ganti">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-3 bg-light border-0" placeholder="Ulangi password baru">
                                </div>
                            </div>

                            {{-- SISI KANAN: ROLE & AKSES --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-shield-alt me-2 text-success"></i>Akses & Tim
                                </h6>

                                {{-- Info Tim --}}
                                <div class="mb-4 p-3 bg-primary bg-opacity-10 rounded-4 shadow-xs border border-primary border-opacity-10">
                                    <label class="small fw-bold d-block mb-1 text-primary">Unit Kerja / Tim:</label>
                                    <span class="h6 fw-bold mb-0 text-dark">{{ $user->team->nama_tim ?? 'Lintas Tim' }}</span>
                                </div>

                                {{-- FITUR TAMBAHAN --}}
                                <div class="mb-4">
                                    @if($user->role == 'Pegawai')
                                        <label class="small fw-bold mb-3 text-dark text-uppercase" style="letter-spacing: 1px;">Fitur Tambahan</label>
                                        <div class="p-3 border rounded-4 bg-white shadow-sm {{ $user->has_super_access ? 'border-danger border-opacity-50' : '' }}">
                                            <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                                                <div class="me-3">
                                                    <label class="form-check-label fw-bold {{ $user->has_super_access ? 'text-danger' : 'text-dark' }} d-block mb-0" for="superAccessSwitch">
                                                        Mode Akses Monitoring
                                                    </label>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        {{ $user->has_super_access ? 'Tiket akses aktif. Matikan untuk kembali ke mode pegawai biasa.' : 'Aktifkan untuk memantau seluruh riwayat kegiatan organisasi.' }}
                                                    </small>
                                                </div>
                                                <input type="hidden" name="has_super_access" value="0">
                                                <input class="form-check-input ms-0 mt-0 shadow-none" type="checkbox" name="has_super_access" role="switch" id="superAccessSwitch" value="1" 
                                                    {{ $user->has_super_access ? 'checked' : '' }}
                                                    style="width: 40px; height: 20px; cursor: pointer;">
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" name="has_super_access" value="1">
                                    @endif
                                </div>

                                {{-- PILIHAN ROLE --}}
                                <div class="mb-3">
                                    <label class="small fw-bold mb-3 text-dark text-uppercase" style="letter-spacing: 1px;">Peran Anda</label>
                                    <div class="d-flex flex-column gap-3">
                                        
                                        @if($user->role == 'Admin')
                                            <div class="p-3 border rounded-4 bg-light shadow-sm border-primary">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-shield"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-primary">Administrator</div>
                                                        <small class="text-muted small">Akses penuh sistem (Terkunci)</small>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="role" value="Admin">
                                            </div>

                                        @elseif(($user->role === 'Katim' || $user->role === 'Kepala') || ($user->has_super_access == 1 && in_array($user->username, ['kepala.bps', 'ketua.tim', 'dodik.hendarto', 'respati.yekti', 'umdatul.ummah', 'ika.rahmawati', 'arif.suroso', 'triana.puji', 'yudhi.prasetyono', 'wicaksono'])))
                                            @php 
                                                $originalRole = ($user->team_id == 8) ? 'Kepala' : 'Katim'; 
                                                $displayRole = ($user->role == 'Pegawai') ? $originalRole : $user->role;
                                            @endphp

                                            <div class="role-selection mb-1">
                                                <input type="radio" name="role" id="roleOriginal" value="{{ $displayRole }}" class="btn-check" {{ $user->role != 'Pegawai' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-light text-start p-3 w-100 rounded-4 shadow-xs border" for="roleOriginal">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-box me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas {{ $displayRole == 'Kepala' ? 'fa-user-tie' : 'fa-users-cog' }}"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $displayRole }}</div>
                                                            <small class="text-muted small">Mode Pejabat / Monitoring Aktif</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="role-selection">
                                                <input type="radio" name="role" id="rolePegawai" value="Pegawai" class="btn-check" {{ $user->role == 'Pegawai' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-light text-start p-3 w-100 rounded-4 shadow-xs border" for="rolePegawai">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-box me-3 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">Pegawai</div>
                                                            <small class="text-muted small">Alihkan ke Mode Pegawai</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @else
                                            <div class="p-3 border rounded-4 bg-light shadow-sm">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">Pegawai</div>
                                                        <small class="text-muted small">Akses laporan pribadi (Terkunci)</small>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="role" value="Pegawai">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-5 pt-2">
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
    .btn-check:checked + .btn-outline-light {
        background-color: #fff !important;
        border-color: #0058a8 !important;
        box-shadow: 0 8px 20px rgba(0, 88, 168, 0.12) !important;
        transform: translateY(-2px);
    }
    .btn-check:checked + .btn-outline-light .fw-bold { color: #0058a8 !important; }
    .btn-check:checked + .btn-outline-light .icon-box { background-color: #0058a8 !important; color: #fff !important; }
    .form-check-input:checked { background-color: #dc3545 !important; border-color: #dc3545 !important; }
    .btn-outline-light { border-color: #f1f5f9 !important; transition: all 0.3s ease; }
    .form-control:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(0, 88, 168, 0.1) !important; border: 1px solid #0058a8 !important; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
</style>
@endsection