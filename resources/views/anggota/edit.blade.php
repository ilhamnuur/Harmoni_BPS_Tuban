@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('manajemen.anggota') }}" class="btn btn-light btn-sm rounded-circle me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Edit Data Anggota</h5>
                            <small class="text-muted">Memperbarui informasi akun: {{ $user->nama_lengkap }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <form action="{{ route('manajemen.anggota.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP *</label>
                                <input type="text" name="nama_lengkap" class="form-control rounded-3 bg-light border-0 @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">USERNAME *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">@</span>
                                    <input type="text" name="username" class="form-control rounded-3 bg-light border-0 @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                </div>
                                @error('username') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">PASSWORD BARU (KOSONGKAN JIKA TIDAK DIGANTI)</label>
                                <input type="password" name="password" class="form-control rounded-3 bg-light border-0 @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">ROLE / JABATAN *</label>
                                <select name="role" class="form-select rounded-3 bg-light border-0" required>
                                    <option value="Pegawai" {{ $user->role == 'Pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="Katim" {{ $user->role == 'Katim' ? 'selected' : '' }}>Ketua Tim (Katim)</option>
                                    <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-muted">PENEMPATAN TIM</label>
                                <select name="team_id" class="form-select rounded-3 bg-light border-0">
                                    <option value="">-- Tanpa Tim --</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id', $user->team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->nama_tim }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('manajemen.anggota') }}" class="btn btn-light rounded-pill px-4 fw-bold text-muted">Batal</a>
                            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection