@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mt-3 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Meja Kerja Persetujuan</h4>
            <p class="text-muted small mb-0">Kelola dan validasi draf SPT (Surat Perintah Tugas) secara digital.</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-4 shadow-sm border border-primary border-opacity-10">
            <i class="fas fa-file-signature text-primary me-2"></i>
            <span class="fw-bold small text-dark">Antrean: {{ $approvals->count() }} Surat</span>
        </div>
    </div>

    {{-- Notifikasi Khusus Kepala --}}
    @if(auth()->user()->role === 'Kepala')
    <div class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center">
        <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3 text-info">
            <i class="fas fa-info-circle fa-lg"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0 text-info">Tips Approval</h6>
            <small>Surat dengan label <span class="badge bg-success rounded-pill">Siap ACC</span> sudah diverifikasi oleh Ketua Tim. Anda bisa langsung menyetujui tanpa perlu cek ulang.</small>
        </div>
    </div>
    @endif

    {{-- Tabel Approval Modern --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 py-3 ps-4">Diajukan</th>
                        <th class="border-0 py-3">Nama Tugas & Creator</th>
                        <th class="border-0 py-3">Status Review</th>
                        <th class="border-0 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvals as $app)
                    <tr class="transition-row">
                        <td class="ps-4">
                            <div class="fw-bold text-dark small">{{ $app->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $app->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary mb-0">{{ $app->title }}</div>
                            <small class="text-muted">Oleh: <b>{{ $app->creator->nama_lengkap }}</b></small>
                        </td>
                        <td>
                            @if($app->reviewed_at)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                    <i class="fas fa-check-double me-1"></i> Siap ACC
                                </span>
                                <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                                    Sudah direview Katim
                                </div>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                                    <i class="fas fa-hourglass-half me-1"></i> Waiting Review
                                </span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Intip --}}
                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-bold shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#modalPreview{{ $app->id }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>

                                {{-- Tombol Setujui --}}
                                <form action="{{ route('assignment.approvals.action', $app->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn {{ $app->reviewed_at ? 'btn-success' : 'btn-primary' }} btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                        <i class="fas fa-check me-1"></i> Setujui
                                    </button>
                                </form>

                                {{-- Tombol Tolak --}}
                                <form action="{{ route('assignment.approvals.action', $app->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL PREVIEW SPT LENGKAP --}}
                    <div class="modal fade" id="modalPreview{{ $app->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-alt text-primary me-2"></i>Detail Draf SPT</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        {{-- Sisi Kiri: Informasi Umum --}}
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-uppercase text-muted mb-2">Informasi Kegiatan</label>
                                            <div class="bg-light p-3 rounded-4 mb-3">
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Judul Tugas:</small>
                                                    <span class="fw-bold text-dark">{{ $app->title }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Rentang Waktu:</small>
                                                    <span class="fw-bold text-dark">
                                                        <i class="far fa-calendar-alt me-1 text-primary"></i>
                                                        {{ $app->event_date->format('d M Y') }} - {{ $app->end_date->format('d M Y') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Target:</small>
                                                    <span class="badge bg-primary rounded-pill">{{ $app->report_target }} Laporan/GC</span>
                                                </div>
                                            </div>

                                            <label class="small fw-bold text-uppercase text-muted mb-2">Isi Perintah (Draf):</label>
                                            <div class="bg-white border p-3 rounded-4 italic text-muted small shadow-sm" style="min-height: 100px;">
                                                "{{ $app->content_surat }}"
                                            </div>
                                        </div>

                                        {{-- Sisi Kanan: Daftar Pegawai --}}
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-uppercase text-muted mb-2">Pegawai yang Ditugaskan</label>
                                            <div class="list-group list-group-flush border rounded-4 overflow-hidden shadow-sm">
                                                @php
                                                    $pekerja = \App\Models\Agenda::where('title', $app->title)
                                                                ->where('user_id', $app->user_id)
                                                                ->where('event_date', $app->event_date)
                                                                ->with('assignee')
                                                                ->get();
                                                @endphp
                                                @foreach($pekerja as $p)
                                                <div class="list-group-item d-flex align-items-center py-2 px-3">
                                                    <div class="avatar-mini bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.65rem; font-weight: 800;">
                                                        {{ strtoupper(substr($p->assignee->nama_lengkap ?? 'P', 0, 1)) }}
                                                    </div>
                                                    <span class="small fw-bold text-dark">{{ $p->assignee->nama_lengkap ?? 'User' }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="mt-3 p-2 bg-success bg-opacity-10 rounded-3 text-center border border-success border-opacity-10">
                                                <small class="text-success fw-bold"><i class="fas fa-info-circle me-1"></i> Total: {{ $pekerja->count() }} Personel</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                                    @if(auth()->user()->role === 'Kepala' && $app->reviewed_at)
                                        <form action="{{ route('assignment.approvals.action', $app->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                                <i class="fas fa-check me-1"></i> Setujui Sekarang
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/amber/empty-folder.svg" style="height: 120px;" class="mb-3 opacity-75">
                            <h6 class="fw-bold text-muted">Antrean Bersih!</h6>
                            <p class="text-muted small">Belum ada draf SPT baru yang perlu diproses.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .transition-row { transition: all 0.2s ease; }
    .transition-row:hover { background-color: #f8fafc !important; }
    .table thead th {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
    }
    .italic { font-style: italic; }
    .avatar-mini { border: 1px solid rgba(0,0,0,0.05); }
</style>
@endsection