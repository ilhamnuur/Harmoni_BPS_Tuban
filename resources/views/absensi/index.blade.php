@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<style>
    .absensi-header {
        background: linear-gradient(135deg, #0058a8 0%, #00b4db 100%);
        color: white; border-radius: 20px; padding: 30px; margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0, 88, 168, 0.15); position: relative; overflow: hidden;
    }

    /* Custom Kalender */
    #calendar {
        background: #fff;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: none;
    }

    .fc-header-toolbar {
        margin-bottom: 25px !important;
        padding: 0 10px;
    }

    .fc-button-primary {
        background-color: var(--bps-blue) !important;
        border-color: var(--bps-blue) !important;
        border-radius: 10px !important;
        text-transform: capitalize;
    }

    .fc-day-today {
        background-color: #f0f7ff !important;
    }

    /* Style Event Cuti */
    .event-cuti {
        background-color: #fef9c3 !important; /* Kuning soft */
        border: 1px solid #facc15 !important;
        color: #854d0e !important; /* Teks cokelat tua */
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .fc-event-title {
        white-space: normal !important;
    }

    /* Floating Action Button (FAB) untuk Input */
    .btn-add-cuti {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--bps-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0, 88, 168, 0.4);
        z-index: 1000;
        transition: 0.3s;
        border: none;
    }

    .btn-add-cuti:hover { transform: scale(1.1); color: white; }
</style>

<div class="container-fluid px-4">
    <div class="absensi-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h3 class="fw-bold mb-1">Kalender Cuti Pegawai</h3>
                <p class="mb-0 opacity-75">Monitoring data cuti tahunan BPS Kabupaten Tuban.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 text-white">
                <h5 class="mb-0 fw-bold"><i class="far fa-calendar-check me-2"></i>Status: Terpantau</h5>
            </div>
        </div>
    </div>

    <div class="alert alert-warning border-0 rounded-4 mb-4 d-flex align-items-center shadow-sm">
        <i class="fas fa-info-circle fs-4 me-3"></i>
        <div>
            <strong>Informasi Sistem:</strong> Pegawai yang namanya tertera pada kalender di bawah ini sedang dalam masa <strong>CUTI</strong> dan secara otomatis <strong>diblokir</strong> dari pengisian laporan pengawasan.
        </div>
    </div>

    <div id="calendar"></div>
</div>

{{-- Tombol Input (Hanya untuk Subbag Umum) --}}
@if(Auth::user()->team && Auth::user()->team->nama_tim == 'Subbagian Umum')
<button class="btn-add-cuti" data-bs-toggle="modal" data-bs-target="#modalInputCuti" title="Input Data Cuti">
    <i class="fas fa-plus fa-lg"></i>
</button>

<div class="modal fade" id="modalInputCuti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-user-slash me-2 text-primary"></i>Input Cuti Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Pegawai</label>
                        <select name="user_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($anggotaTim as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Mulai Cuti</label>
                            <input type="date" name="start_date" class="form-control rounded-3" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Berakhir</label>
                            <input type="date" name="end_date" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Keterangan Cuti</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="Contoh: Cuti Tahunan"></textarea>
                    </div>
                    <input type="hidden" name="status" value="Cuti">
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Simpan Data Cuti</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: [
                /* DATA DARI DATABASE NANTI DI-LOOP DI SINI */
                @foreach($absensi as $absen)
                {
                    title: '{{ $absen->user->nama_lengkap }} (CUTI)',
                    start: '{{ $absen->start_date }}',
                    end: '{{ \Carbon\Carbon::parse($absen->end_date)->addDay()->format("Y-m-d") }}', // +1 hari agar FullCalendar menampilkan sampai akhir tanggal
                    className: 'event-cuti'
                },
                @endforeach
            ],
            eventDisplay: 'block',
        });
        calendar.render();
    });
</script>
@endsection