@extends('layouts.app')

@section('content')
<style>
    :root { --bps-blue: #0058a8; --bps-text: #1e293b; }
    .card-assignment { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .form-section-title { 
        font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; 
        letter-spacing: 1.5px; margin-bottom: 1.25rem; display: flex; align-items: center;
    }
    .form-section-title i { margin-right: 10px; color: var(--bps-blue); }
    .form-section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; margin-left: 15px; }
    .user-selection-container { border: 1px solid #e2e8f0; border-radius: 15px; background: #fff; overflow: hidden; }
    .user-selection-box { max-height: 400px; overflow-y: auto; }
    .user-group-label { background: #f8fafc; color: #64748b; font-weight: 800; padding: 12px 15px; font-size: 0.65rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 10; }
    .user-item { padding: 12px 15px; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; cursor: pointer; transition: 0.2s; }
    .user-item:hover { background-color: #f0f7ff; }
    .is-busy-text { font-size: 0.65rem; color: #ef4444; font-weight: 700; margin-left: auto; background: #fef2f2; padding: 2px 8px; border-radius: 4px; }
    .custom-chk { width: 20px; height: 20px; border-radius: 6px; border: 2px solid #cbd5e1; margin-right: 12px; display: flex; align-items: center; justify-content: center; background: #fff; flex-shrink: 0; }
    .user-check:checked + .custom-chk { background-color: var(--bps-blue); border-color: var(--bps-blue); }
    .user-check:checked + .custom-chk::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: #fff; font-size: 10px; }
    .form-control, .form-select { border-radius: 10px; }
</style>

<div class="container-fluid px-4 pb-5">
    {{-- Tampilkan Error Validasi --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="small">Terjadi Kesalahan:</strong>
            </div>
            <ul class="mb-0 small fw-bold">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form id="formAssignment" action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- KIRI: DETAIL KEGIATAN --}}
            <div class="col-lg-7">
                <div class="card card-assignment shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary"><i class="fas fa-edit fa-lg"></i></div>
                            <div>
                                <h4 class="fw-bold mb-0">Plotting Penugasan</h4>
                                <p class="text-muted small mb-0">Atur kegiatan lapangan atau agenda rapat dinas.</p>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-info-circle"></i>1. Informasi Utama</div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2 text-danger">Jenis Kegiatan</label>
                                <select name="activity_type_id" id="activity_type_id" class="form-select border-danger border-opacity-25" required>
                                    <option value="1" {{ old('activity_type_id') == 1 ? 'selected' : '' }}>Tugas Lapangan</option>
                                    <option value="2" {{ old('activity_type_id') == 2 ? 'selected' : '' }}>Rapat Dinas</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Nama Kegiatan</label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Rapat Koordinasi" value="{{ old('title') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Deskripsi / Instruksi Tambahan</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2 text-primary" id="st-label">Nomor Surat Tugas (ST)</label>
                                <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control border-primary border-opacity-25" placeholder="B-123/..." value="{{ old('nomor_surat_tugas') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Tujuan / Lokasi</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-calendar-alt"></i>2. Waktu & Dokumen</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4" id="col-start-date">
                                <label class="small fw-bold mb-2" id="label-event-date">Tanggal Mulai</label>
                                <input type="date" name="event_date" id="event_date" class="form-control date-check" value="{{ old('event_date', date('Y-m-d')) }}" required>
                            </div>
                            {{-- Field Tanggal Selesai yang akan di-hide jika Rapat --}}
                            <div class="col-md-4" id="end-date-container">
                                <label class="small fw-bold mb-2">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" class="form-control date-check" value="{{ old('end_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4" id="time-field" style="display: none;">
                                <label class="small fw-bold mb-2 text-primary">Jam Mulai Rapat</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                            </div>
                        </div>

                        <div id="rapat-fields" style="display: none;" class="bg-light p-3 rounded-4 mb-4 border">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-2 text-primary"><i class="fas fa-pen-nib me-1"></i> Tunjuk Notulis</label>
                                    <select name="notulis_id" class="form-select" id="notulis-select">
                                        <option value="">-- Pilih dari petugas --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-2">Materi Rapat (PDF/PPTX)</label>
                                    <input type="file" name="materi_path" class="form-control bg-white">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="small fw-bold mb-2"><i class="fas fa-file-pdf me-1 text-danger"></i> Upload Surat Tugas (PDF)</label>
                            <input type="file" name="surat_tugas" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: PILIH PETUGAS --}}
            <div class="col-lg-5">
                <div class="card card-assignment shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-users"></i>3. Pilih Petugas / Peserta</div>
                        
                        <div class="user-selection-container shadow-sm mb-4">
                            <div class="user-selection-box">
                                <div class="user-group-label text-primary">Ketua Tim / Fungsional</div>
                                @foreach($katims as $k)
                                    <div class="user-item petugas-row" data-id="{{ $k->id }}" data-name="{{ $k->nama_lengkap }}">
                                        <input type="checkbox" name="assigned_to[]" value="{{ $k->id }}" id="user_{{ $k->id }}" class="user-check d-none">
                                        <div class="custom-chk"></div>
                                        <div>
                                            <span class="user-name small fw-bold text-dark d-block">{{ $k->nama_lengkap }}</span>
                                            <small class="text-muted" style="font-size: 0.65rem;">Katim {{ $k->team->nama_tim ?? '' }}</small>
                                        </div>
                                        <span class="is-busy-text d-none" id="status_{{ $k->id }}">Ada Agenda</span>
                                    </div>
                                @endforeach

                                <div class="user-group-label text-success">Staf / Mitra Lapangan</div>
                                @foreach($pegawais as $p)
                                    <div class="user-item petugas-row" data-id="{{ $p->id }}" data-name="{{ $p->nama_lengkap }}">
                                        <input type="checkbox" name="assigned_to[]" value="{{ $p->id }}" id="user_{{ $p->id }}" class="user-check d-none">
                                        <div class="custom-chk"></div>
                                        <span class="user-name small fw-bold text-dark">{{ $p->nama_lengkap }}</span>
                                        <span class="is-busy-text d-none" id="status_{{ $p->id }}">Ada Agenda</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg mt-2" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i> Konfirmasi & Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateNotulisDropdown() {
        const notulisSelect = $('#notulis-select');
        const selectedNotulis = notulisSelect.val();
        notulisSelect.html('<option value="">-- Pilih dari petugas --</option>');
        
        $('.user-check:checked').each(function() {
            const userId = $(this).val();
            const userName = $(this).closest('.petugas-row').data('name');
            const isSelected = (userId == selectedNotulis) ? 'selected' : '';
            notulisSelect.append(`<option value="${userId}" ${isSelected}>${userName}</option>`);
        });
    }

    $(document).ready(function() {
        // Toggle UI Rapat vs Tugas
        $('#activity_type_id').on('change', function() {
            const type = $(this).val();
            const inputST = $('#nomor_surat_tugas');
            const labelST = $('#st-label');
            const labelEventDate = $('#label-event-date');

            if (type == '2') { // RAPAT DINAS
                $('#rapat-fields, #time-field').slideDown();
                $('#end-date-container').hide(); // Sembunyikan Tanggal Selesai
                
                // Samakan nilai end_date dengan event_date agar tidak error validasi backend
                $('#end_date').val($('#event_date').val());
                
                labelEventDate.text('Tanggal Pelaksanaan');
                inputST.prop('required', false);
                labelST.text('Nomor Surat Tugas (Opsional)');
            } else { // TUGAS LAPANGAN
                $('#rapat-fields, #time-field').slideUp();
                $('#end-date-container').show(); // Munculkan kembali
                
                labelEventDate.text('Tanggal Mulai');
                inputST.prop('required', true);
                labelST.text('Nomor Surat Tugas (ST)');
            }
            checkAvailability();
        }).trigger('change');

        // Logika Klik Baris untuk Centang Petugas
        $('.user-item').on('click', function(e) {
            if (!$(e.target).is('input')) {
                const cb = $(this).find('.user-check');
                cb.prop('checked', !cb.prop('checked')).trigger('change');
            }
        });

        $('.user-check').on('change', function() {
            updateNotulisDropdown();
        });

        // Sinkronisasi Tanggal
        $('#event_date').on('change', function() {
            let startVal = $(this).val();
            
            // Jika tipe rapat, otomatis samakan end_date di background
            if($('#activity_type_id').val() == '2') {
                $('#end_date').val(startVal);
            } else {
                $('#end_date').attr('min', startVal);
                if($('#end_date').val() < startVal) {
                    $('#end_date').val(startVal);
                }
            }
            checkAvailability();
        });

        $('#end_date').on('change', function() { checkAvailability(); });

        // Proteksi Submit
        $('#formAssignment').on('submit', function(e) {
            if ($('.user-check:checked').length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu petugas!');
                return false;
            }
            $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');
        });

        function checkAvailability() {
            const start = $('#event_date').val();
            const end = $('#end_date').val();
            if (start && end) {
                $.get("{{ route('assignment.check-availability') }}", { start_date: start, end_date: end }, function(res) {
                    const busyIds = res.busy_users.map(String);
                    $('.petugas-row').each(function() {
                        const id = $(this).data('id').toString();
                        if (busyIds.includes(id)) { $('#status_' + id).removeClass('d-none'); }
                        else { $('#status_' + id).addClass('d-none'); }
                    });
                });
            }
        }
        checkAvailability();
    });
</script>
@endsection