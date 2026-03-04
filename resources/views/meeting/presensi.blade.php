@extends('layouts.app')

@section('content')
<style>
    .card-presensi {
        border: none;
        border-radius: 25px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .header-presensi {
        background: linear-gradient(135deg, #0058a8 0%, #003d75 100%);
        padding: 30px;
        color: white;
        text-align: center;
    }
    .signature-wrapper {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        position: relative;
        touch-action: none; 
        margin-bottom: 15px;
    }
    #signature-pad {
        width: 100%;
        height: 250px;
        cursor: crosshair;
    }
    .user-info-box {
        background: #f1f5f9;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 25px;
    }
    .btn-simpan {
        background: #0058a8;
        border: none;
        padding: 12px;
        font-weight: bold;
        transition: all 0.3s;
    }
    .btn-simpan:hover {
        background: #003d75;
        transform: translateY(-2px);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-presensi">
                <div class="header-presensi">
                    <i class="fas fa-file-signature fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Daftar Hadir Digital</h3>
                    <p class="small opacity-75 mb-0">BPS Kabupaten Tuban</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('success'))
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Berhasil!</h4>
                            <p class="text-muted">{{ session('success') }}</p>
                            <p class="small text-muted mt-3">Mengalihkan ke halaman agenda dalam <span id="timer">3</span> detik...</p>
                            <a href="{{ route('meeting.index') }}" class="btn btn-primary rounded-pill px-5">Kembali Sekarang</a>
                        </div>
                    @elseif($alreadySigned)
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-check text-primary" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Sudah Mengisi</h4>
                            <p class="text-muted">Kehadiran Anda pada rapat <strong>{{ $agenda->title }}</strong> sudah tercatat.</p>
                            <a href="{{ route('meeting.index') }}" class="btn btn-outline-primary rounded-pill px-5">Kembali</a>
                        </div>
                    @else
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1">{{ $agenda->title }}</h5>
                            <p class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i> {{ $agenda->event_date->format('d M Y') }} 
                                <i class="fas fa-clock ms-2 me-1"></i> {{ $agenda->start_time ?? 'WIB' }}
                            </p>
                        </div>

                        <div class="user-info-box">
                            <div class="row small">
                                <div class="col-4 text-muted">Nama Lengkap</div>
                                <div class="col-8 fw-bold text-dark">: {{ auth()->user()->nama_lengkap }}</div>
                                <div class="col-4 text-muted mt-2">NIP</div>
                                <div class="col-8 fw-bold text-dark mt-2">: {{ auth()->user()->nip }}</div>
                            </div>
                        </div>

                        <form action="{{ route('meeting.presensi.store') }}" method="POST" id="signature-form">
                            @csrf
                            <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
                            <input type="hidden" name="signature" id="signature-value">

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="small fw-bold text-secondary">Goreskan Tanda Tangan:</label>
                                <button type="button" id="clear-btn" class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                                    <i class="fas fa-eraser me-1"></i> Hapus & Ulangi
                                </button>
                            </div>

                            <div class="signature-wrapper">
                                <canvas id="signature-pad"></canvas>
                            </div>

                            <p class="small text-muted mb-4 text-center">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Gunakan jari atau stylus untuk menandatangani layar.
                            </p>

                            <button type="submit" class="btn btn-primary btn-simpan w-100 rounded-pill shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Kehadiran
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Library Signature Pad JS --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- LOGIKA OTOMATIS REDIRECT ---
        @if(session('success'))
            let timeLeft = 3;
            const timerDisplay = document.getElementById('timer');
            const countdown = setInterval(function() {
                timeLeft--;
                if(timerDisplay) timerDisplay.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = "{{ route('meeting.index') }}";
                }
            }, 1000);
        @endif

        // --- LOGIKA SIGNATURE PAD ---
        const canvas = document.getElementById('signature-pad');
        if(canvas) {
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }

            window.onresize = resizeCanvas;
            resizeCanvas();

            document.getElementById('clear-btn').addEventListener('click', function() {
                signaturePad.clear();
            });

            const form = document.getElementById('signature-form');
            form.addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert("Silakan tanda tangan terlebih dahulu!");
                } else {
                    const dataURL = signaturePad.toDataURL('image/png');
                    document.getElementById('signature-value').value = dataURL;
                }
            });
        }
    });
</script>
@endsection