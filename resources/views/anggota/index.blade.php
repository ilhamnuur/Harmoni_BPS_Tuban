@extends('layouts.app')

@section('content')
<style>
    /* 1. Paksa Container tetap di dalam layar */
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden; /* Hilangkan scroll samping di body */
    }

    .card-members {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        background: #fff;
    }

    /* 2. Fix Table agar tidak meluber */
    .table-responsive {
        border: none;
        margin: 0;
        overflow-x: auto; /* Scroll hanya muncul di dalam tabel jika sangat sempit */
    }

    .table-members {
        width: 100%;
        margin-bottom: 0;
        table-layout: fixed; /* Rahasia agar kolom tidak melar sendiri */
    }

    .table-members thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 800;
        padding: 15px 10px;
        border: none;
    }

    /* 3. Atur Lebar Kolom secara Presisi */
    .col-nama { width: 25%; }
    .col-user { width: 15%; }
    .col-role { width: 15%; }
    .col-tim  { width: 15%; }
    .col-tgl  { width: 15%; }
    .col-aksi { width: 15%; }

    .table-members tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap; /* Cegah teks turun ke bawah */
        overflow: hidden;
        text-overflow: ellipsis; /* Kalau kepanjangan jadi titik-titik (...) */
    }

    /* 4. Avatar & Badges */
    .avatar-mini {
        width: 32px;
        height: 32px;
        background: #0058a8;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .role-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 50px;
    }

    /* 5. Action Buttons Compact */
    .btn-action-group {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .btn-mini {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        transition: 0.2s;
    }
    .btn-edit { background: #fffbeb; color: #d97706; }
    .btn-delete { background: #fff1f0; color: #ef4444; }
    .btn-mini:hover { transform: scale(1.1); }

    @media (max-width: 768px) {
        .table-members { table-layout: auto; } /* Biar fleksibel di HP */
    }
</style>

<div class="container-fluid">
    <div class="card card-members">
        <div class="card-body p-0">
            {{-- Header --}}
            <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Data Anggota</h5>
                    <p class="text-muted small mb-0">Total: {{ $anggota->total() }} Personel BPS Tuban</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('manajemen.anggota') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" 
                               placeholder="Cari..." value="{{ request('search') }}" style="width: 180px;">
                    </form>
                    <a href="{{ route('manajemen.anggota.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-members">
                    <thead>
                        <tr>
                            <th class="ps-4 col-nama">Nama Lengkap</th>
                            <th class="col-user">Username</th>
                            <th class="text-center col-role">Role</th>
                            <th class="text-center col-tim">Tim</th>
                            <th class="text-center col-tgl">Bergabung</th>
                            <th class="text-center pe-4 col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $a)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-mini me-2">{{ substr($a->nama_lengkap, 0, 1) }}</div>
                                    <div class="text-truncate fw-bold text-dark" style="max-width: 150px;" title="{{ $a->nama_lengkap }}">
                                        {{ $a->nama_lengkap }}
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-primary small">@ {{ $a->username }}</span></td>
                            <td class="text-center">
                                @if($a->role == 'Admin')
                                    <span class="role-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">Admin</span>
                                @elseif($a->role == 'Katim')
                                    <span class="role-badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10">Katim</span>
                                @else
                                    <span class="role-badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">Pegawai</span>
                                @endif
                            </td>
                            <td class="text-center small fw-medium text-muted">
                                {{ $a->team->nama_tim ?? '-' }}
                            </td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/y') }}
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-action-group">
                                    <a href="{{ route('manajemen.anggota.edit', $a->id) }}" class="btn-mini btn-edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('manajemen.anggota.destroy', $a->id) }}" method="POST" id="del-{{ $a->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $a->id }}, '{{ $a->nama_lengkap }}')" class="btn-mini btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Data Kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $anggota->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus?',
            text: name + " akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('del-' + id).submit();
        });
    }
</script>
@endsection