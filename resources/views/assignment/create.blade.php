@extends('layouts.app')

@section('content')
<style>
    :root { --bps-blue: #0058a8; --bps-text: #1e293b; }
    .card-assignment { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    
    /* Perbaikan agar title dan tombol sejajar */
    .form-section-title { 
        font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; 
        letter-spacing: 1.5px; margin-bottom: 1.25rem; display: flex; align-items: center;
        width: 100%; /* Pastikan mengambil ruang penuh */
    }
    .form-section-title i { margin-right: 10px; color: var(--bps-blue); }
    
    /* Garis pemisah disesuaikan agar tidak menabrak tombol di kanan */
    .form-section-title::after { 
        content: ""; 
        flex: 1; 
        height: 1px; 
        background: #f1f5f9; 
        margin-left: 15px; 
    }

    .user-selection-container { border: 1px solid #e2e8f0; border-radius: 15px; background: #fff; overflow: hidden; }
    .user-selection-box { max-height: 500px; overflow-y: auto; }
    
    .user-group-label { 
        background: #f8fafc; color: #64748b; font-weight: 800; padding: 12px 15px; font-size: 0.65rem; 
        text-transform: uppercase; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 10;
        display: flex; justify-content: space-between; align-items: center;
    }
    
    .user-item { padding: 12px 15px; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; cursor: pointer; transition: 0.2s; }
    .user-item:hover { background-color: #f0f7ff; }
    
    .status-badge { font-size: 0.6rem; font-weight: 700; margin-left: auto; padding: 2px 8px; border-radius: 4px; border: 1px solid transparent; }
    .is-busy-text { color: #f59e0b; background: #fffbeb; border-color: #fef3c7; }
    .is-leave-text { color: #ef4444; background: #fef2f2; border-color: #fee2e2; }

    .custom-chk { width: 20px; height: 20px; border-radius: 6px; border: 2px solid #cbd5e1; margin-right: 12px; display: flex; align-items: center; justify-content: center; background: #fff; flex-shrink: 0; }
    .user-check:checked + .custom-chk { background-color: var(--bps-blue); border-color: var(--bps-blue); }
    .user-check:checked + .custom-chk::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: #fff; font-size: 10px; }
    
    .form-control, .form-select { border-radius: 10px; padding: 0.75rem; min-height: 48px; }
    .required-star { color: #ef4444; margin-left: 3px; font-weight: bold; }
    
    /* Styling Tombol Pilih Semua yang lebih rapi */
    #btnSelectAll {
        border: 1.5px solid var(--bps-blue);
        color: var(--bps-blue);
        background: white;
        white-space: nowrap;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 5px 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-left: 15px; /* Memberi jarak dari garis pemisah */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    #btnSelectAll:hover {
        background-color: var(--bps-blue);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 88, 168, 0.15);
    }

    #btnSelectAll:active {
        transform: translateY(0);
    }

    .conflict-card { background: #fffbeb; border-radius: 12px; padding: 12px; margin-bottom: 10px; border-left: 5px solid #f59e0b; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .leave-card { background: #fef2f2; border-radius: 12px; padding: 12px; margin-bottom: 10px; border-left: 5px solid #ef4444; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
</style>

<div class="container-fluid px-4 pb-5">
    <div class="mb-4 mt-3">
        <a href="{{ route('assignment.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-muted shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form id="formAssignment" action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- SISI KIRI: DETAIL KEGIATAN --}}
<div class="col-lg-7">
    <div class="card card-assignment shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body p-4">
            {{-- Header Card --}}
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                    <i class="fas fa-calendar-plus fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Plotting Penugasan</h4>
                    <p class="text-muted small mb-0">Manajemen plotting kegiatan personil BPS Tuban.</p>
                </div>
            </div>

            {{-- 1. INFORMASI UTAMA --}}
            <div class="form-section-title"><i class="fas fa-info-circle"></i>1. Informasi Utama</div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="small fw-bold mb-2">Asal Tim Penugasan <span class="text-danger">*</span></label>
                    <select name="team_id" class="form-select border-primary border-opacity-25 shadow-sm" required>
                        <option value="">-- Pilih Tim Penanggung Jawab --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Jenis Kegiatan<span class="required-star">*</span></label>
                    <select name="activity_type_id" id="activity_type_id" class="form-select border-primary border-opacity-25" required>
                        <option value="1">Tugas Lapangan</option>
                        <option value="2">Rapat Dinas</option>
                        <option value="3">Dinas Luar</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold mb-2">Nama Kegiatan<span class="required-star">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Input nama kegiatan..." required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="small fw-bold mb-2">Nomor Surat Tugas<span class="required-star">*</span></label>
                    <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control" placeholder="Contoh: B-123/BPS/35230/..." required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Target Laporan / Translok<span class="required-star">*</span></label>
                    <input type="number" name="report_target" id="report_target" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" class="form-control" rows="1"></textarea>
                </div>
            </div>

            {{-- 2. WAKTU & DOKUMEN --}}
            <div class="form-section-title"><i class="fas fa-clock"></i>2. Waktu & Dokumen</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="small fw-bold mb-2" id="label-event-date">Tanggal Mulai<span class="required-star">*</span></label>
                    <input type="date" name="event_date" id="event_date" class="form-control" required>
                </div>
                <div class="col-md-6" id="end-date-container">
                    <label class="small fw-bold mb-2">Tanggal Selesai<span class="required-star">*</span></label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
                <div class="col-md-6" id="time-field" style="display: none;">
                    <label class="small fw-bold mb-2">Jam Kegiatan<span class="required-star">*</span></label>
                    <input type="time" name="start_time" id="start_time" class="form-control">
                </div>
            </div>

            {{-- --- KOTAK NOTULIS (KHUSUS RAPAT) --- --}}
            <div id="rapat-fields" style="display: none;" class="mb-4">
                <div class="p-3 border border-warning border-opacity-25 rounded-4 bg-warning bg-opacity-10">
                    <label class="small fw-bold mb-2 text-dark"><i class="fas fa-pen-nib me-1"></i> Pilih Notulis Rapat<span class="required-star">*</span></label>
                    <select name="notulis_id" id="notulis-select" class="form-select border-warning">
                        <option value="">-- Pilih dari petugas terpilih --</option>
                        {{-- Akan diisi otomatis via JS saat petugas dipilih di sisi kanan --}}
                    </select>
                    <small class="text-muted" style="font-size: 0.65rem;">* Notulis wajib dipilih dari daftar petugas yang ditugaskan.</small>
                </div>
            </div>

            {{-- 3. DOKUMEN & PERSETUJUAN --}}
            <div class="form-section-title"><i class="fas fa-file-signature"></i>3. Dokumen & Persetujuan</div>
            <div class="mb-4 p-3 border rounded-4 bg-light">
                <label class="small fw-bold mb-3 d-block">Metode Dokumen Tugas<span class="required-star">*</span></label>
                <div class="d-flex gap-4 mb-3">
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="mode_surat" id="modeUpload" value="upload" checked>
                        <label class="form-check-label fw-bold small" for="modeUpload">Upload PDF</label>
                    </div>
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="mode_surat" id="modeGenerate" value="generate">
                        <label class="form-check-label fw-bold small" for="modeGenerate">Ketik Surat</label>
                    </div>
                </div>

                {{-- Section Upload --}}
                <div id="section-upload">
                    <div class="mb-0">
                        <label class="small fw-bold mb-2" id="label-upload">File PDF Surat Tugas<span class="required-star">*</span></label>
                        <input type="file" name="surat_tugas" id="surat_tugas" class="form-control" accept="application/pdf">
                    </div>
                </div>

                        <div id="section-generate" style="display: none;">
    {{-- 1. PILIHAN MODE CETAK (Hanya Muncul di SPT Lapangan) --}}
    <div class="mb-3 p-2 border rounded-3 bg-white shadow-sm" id="print-mode-container">
        <label class="small fw-bold mb-2 d-block text-primary"><i class="fas fa-print me-1"></i> Mode Output Surat</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="print_mode" id="modePerorang" value="perorang" checked>
                <label class="form-check-label small fw-bold" for="modePerorang">Per Orang</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="print_mode" id="modeKolektif" value="kolektif">
                <label class="form-check-label small fw-bold" for="modeKolektif">Kolektif (Lampiran)</label>
            </div>
        </div>
    </div>

    {{-- 2. INPUT KHUSUS SPT (Hanya Muncul jika Tugas Lapangan) --}}
    <div id="spt-fields">
        <div class="mb-3">
            <label class="small fw-bold mb-1">Menimbang</label>
            <textarea name="menimbang" class="form-control" rows="2" placeholder="Kosongkan jika ingin format default BPS..."></textarea>
        </div>
        <div class="mb-3">
            <label class="small fw-bold mb-1">Mengingat</label>
            <textarea name="mengingat" class="form-control" rows="2" placeholder="Kosongkan jika ingin format default BPS..."></textarea>
        </div>
    </div>

    {{-- 3. INPUT KHUSUS MEMO (Hanya Muncul jika Rapat/Dinas Luar) --}}
    <div id="memo-fields" style="display: none;">
        <div class="mb-3">
            <label class="small fw-bold mb-1">Yth (Kepada)</label>
            <input type="text" name="yth" class="form-control" value="Pegawai BPS Kabupaten Tuban" placeholder="Contoh: Pegawai BPS Kabupaten Tuban">
        </div>
        <div class="mb-3">
            <label class="small fw-bold mb-1">Lokasi Kegiatan <span class="required-star">*</span></label>
            <input type="text" name="location" id="location" class="form-control" placeholder="Contoh: Ruang Rapat BPS Tuban / Cafe Bestie">
        </div>
    </div>

    {{-- 4. ISI PERINTAH / KETERANGAN --}}
    <div class="mb-3">
        <label class="small fw-bold mb-2" id="label-content-surat">Isi Perintah / Keterangan Tambahan<span class="required-star">*</span></label>
        <textarea name="content_surat" id="content_surat" class="form-control" rows="3" placeholder="Tulis rincian tugas atau agenda rapat di sini..."></textarea>
    </div>

    {{-- 5. PENGATURAN PERSETUJUAN --}}
    <div class="p-3 bg-white border rounded-4 shadow-sm">
        <label class="small fw-bold mb-3 d-block text-primary"><i class="fas fa-shield-alt me-1"></i> Pengaturan Persetujuan</label>
        <div class="d-flex gap-3 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="approval_type" id="appSingle" value="single" checked>
                <label class="form-check-label small fw-bold" for="appSingle">Single (Kepala)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="approval_type" id="appMultiple" value="multiple">
                <label class="form-check-label small fw-bold" for="appMultiple">Multiple (Katim & Kepala)</label>
            </div>
        </div>
        
        <div id="reviewer-container" style="display: none;" class="mb-3">
            <label class="small fw-bold mb-2">Pilih Ketua Tim (Reviewer)</label>
            <select name="reviewer_id" id="reviewer_id" class="form-select">
                <option value="">-- Pilih Katim --</option>
                @foreach($katims as $k) <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option> @endforeach
            </select>
        </div>

        <div>
            <label class="small fw-bold mb-2">Pilih Kepala BPS (Penandatangan)</label>
            <select name="approver_id" id="approver_id" class="form-select">
                <option value="">-- Pilih Kepala --</option>
                @foreach($kepalas as $k) <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option> @endforeach
            </select>
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: DAFTAR PETUGAS --}}
        <div class="col-lg-5">
            <div class="card card-assignment shadow-sm h-100 border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="form-section-title mb-0" style="flex: 1;"><i class="fas fa-users"></i>3. Daftar Petugas</div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" id="btnSelectAll">Pilih Semua</button>
                    </div>
                    
                    <div class="user-selection-container shadow-sm mb-4 border rounded-3 overflow-hidden">
                        <div class="user-selection-box" style="max-height: 600px; overflow-y: auto;">
                            
                            {{-- AKUN KHUSUS (ketua.tim) --}}
                            @if($akunKhusus)
                                <div class="user-group-label bg-warning bg-opacity-10 text-dark fw-bold px-3 py-2 border-bottom">
                                    <i class="fas fa-star me-2 text-warning"></i>Akun Khusus
                                </div>
                                <div class="user-item petugas-row" data-id="{{ $akunKhusus->id }}" data-name="{{ $akunKhusus->nama_lengkap }}">
                                    <input type="checkbox" name="assigned_to[]" value="{{ $akunKhusus->id }}" class="user-check d-none">
                                    <div class="custom-chk"></div>
                                    <span class="user-name small fw-bold text-primary">{{ $akunKhusus->nama_lengkap }} ({{ $akunKhusus->username }})</span>
                                    <span class="status-badge is-busy-text d-none" id="status_busy_{{ $akunKhusus->id }}">Ada Agenda</span>
                                </div>
                            @endif

                            @php $groups = ['Kepala BPS' => $kepalas, 'Ketua Tim' => $katims, 'Staf' => $pegawais]; @endphp
                            @foreach($groups as $label => $users)
                                <div class="user-group-label px-3 py-2 bg-light border-bottom border-top small fw-bold text-muted">{{ $label }}</div>
                                @foreach($users as $u)
                                    <div class="user-item petugas-row" data-id="{{ $u->id }}" data-name="{{ $u->nama_lengkap }}">
                                        <input type="checkbox" name="assigned_to[]" value="{{ $u->id }}" class="user-check d-none">
                                        <div class="custom-chk"></div>
                                        <span class="user-name small fw-bold text-dark">{{ $u->nama_lengkap }}</span>
                                        <span class="status-badge is-busy-text d-none" id="status_busy_{{ $u->id }}">Ada Agenda</span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" id="btnConfirmSubmit">Konfirmasi Penugasan</button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Inisialisasi variabel global
    window.currentAgendaDetails = {};
    window.currentLeaveUsers = [];
    window.globalConflicts = [];

    // --- 1. FUNGSI LOGIKA FORM UTAMA ---
    $('#activity_type_id').on('change', function() {
        const val = $(this).val();
        const star = '<span class="required-star">*</span>';

        // RESET SEMUA REQUIRED AWAL (PENTING AGAR TIDAK STUCK)
        $('#nomor_surat_tugas, #end_date, #start_time, #notulis-select, #surat_tugas, #location, #content_surat, #report_target').prop('required', false);

        if (val == '1') { 
            // === TUGAS LAPANGAN (SPT) ===
            $('#spt-fields').slideDown();
            $('#memo-fields').slideUp();
            $('#rapat-fields').hide(); 
            $('#print-mode-container').show();
            
            // Show: Tanggal Mulai, Tanggal Selesai, Translok. Hide: Jam.
            $('#end-date-container').show();
            $('#time-field').hide();
            $('#report_target').closest('.col-md-6').show(); // Translok muncul
            
            // Update Label
            $('#label-event-date').html('Tanggal Mulai' + star);
            $('#label-content-surat').html('Isi Perintah Tugas' + star);
            
            // Pasang Required
            $('#nomor_surat_tugas, #event_date, #end_date, #content_surat, #report_target').prop('required', true);
        } 
        else { 
            // === RAPAT (2) ATAU DINAS LUAR (3) (MEMORANDUM) ===
            $('#spt-fields').slideUp();
            $('#memo-fields').slideDown();
            $('#print-mode-container').hide();
            
            // Show: Jam, Tanggal Pelaksanaan. Hide: Translok.
            $('#time-field').show();
            $('#report_target').closest('.col-md-6').hide(); // Translok sembunyi
            
            // Update Label
            $('#label-event-date').html('Tanggal Pelaksanaan' + star);
            $('#label-content-surat').html('Keterangan / Agenda' + star);
            
            // Required Dasar Memo
            $('#nomor_surat_tugas, #event_date, #start_time, #location, #content_surat').prop('required', true);

            if (val == '2') { 
                // KHUSUS RAPAT DINAS
                $('#rapat-fields').slideDown(); // Notulis muncul
                $('#end-date-container').hide(); // Selesai sembunyi
                $('#notulis-select').prop('required', true);
            } else { 
                // KHUSUS DINAS LUAR
                $('#rapat-fields').hide(); // Notulis sembunyi
                $('#end-date-container').show(); // Selesai muncul (karena DL bisa berhari-hari)
                $('#end_date').prop('required', true);
            }
        }

        // Jalankan ulang logika mode surat (Upload vs Ketik)
        $('input[name="mode_surat"]:checked').trigger('change');
        if(typeof checkAvailability === "function") checkAvailability();
    });

    // --- 2. LOGIKA TOGGLE MODE SURAT (UPLOAD vs KETIK) ---
    $('input[name="mode_surat"]').on('change', function() {
        const mode = $(this).val();
        const type = $('#activity_type_id').val();

        if (mode === 'generate') {
            $('#section-generate').slideDown();
            $('#section-upload').slideUp();
            
            // Required Ketik
            $('#content_surat, #approver_id').prop('required', true);
            if(type != '1') $('#location').prop('required', true);
            
            $('#surat_tugas').prop('required', false);
        } else {
            $('#section-generate').slideUp();
            $('#section-upload').slideDown();
            
            // Bersihkan Required Ketik (Supaya tidak menghalangi mode upload)
            $('#content_surat, #approver_id, #location, #notulis-select, #reviewer_id, #yth, #mengingat, #menimbang').prop('required', false);
            
            $('#surat_tugas').prop('required', true);
        }
    });

    // --- 3. LOGIKA PILIH PETUGAS & CENTANG (FIXED) ---
    $(document).on('click', '.user-item', function(e) {
        if ($(e.target).is('input')) return; 
        const cb = $(this).find('.user-check');
        cb.prop('checked', !cb.prop('checked')).trigger('change');
    });

    $(document).on('change', '.user-check', function() {
        updateNotulisDropdown();
    });

    $('#btnSelectAll').on('click', function() {
        const checkboxes = $('.user-check');
        const isAllChecked = checkboxes.length === $('.user-check:checked').length;
        checkboxes.prop('checked', !isAllChecked).trigger('change');
        $(this).html(!isAllChecked ? '<i class="fas fa-times me-1"></i> Batal Semua' : '<i class="fas fa-check-double me-1"></i> Pilih Semua');
    });

    function updateNotulisDropdown() {
        const select = $('#notulis-select');
        const currentVal = select.val();
        select.html('<option value="">-- Pilih dari petugas terpilih --</option>');
        $('.user-check:checked').each(function() {
            const id = $(this).val();
            const name = $(this).closest('.petugas-row').data('name');
            select.append(`<option value="${id}" ${id == currentVal ? 'selected' : ''}>${name}</option>`);
        });
    }

    // --- 4. VALIDASI & SUBMIT ---
    $('#btnConfirmSubmit').on('click', function() {
        const form = document.getElementById('formAssignment');
        
        // Cek validasi HTML5
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const selected = $('.user-check:checked');
        if (selected.length === 0) {
            Swal.fire({ title: 'Petugas Belum Dipilih!', text: 'Pilih minimal satu petugas di daftar kanan.', icon: 'warning', confirmButtonColor: '#0058a8' });
            return;
        }

        // Tampilkan Loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        form.submit();
    });

    // --- 5. LOGIKA TANGGAL ---
    $('#event_date, #end_date').on('change', function() {
        $('#end_date').attr('min', $('#event_date').val());
        if($('#activity_type_id').val() == '2') {
            $('#end_date').val($('#event_date').val());
        }
        checkAvailability();
    });

    function checkAvailability() {
        const start = $('#event_date').val();
        const end = $('#end_date').val() || start;
        if (start) {
            $.get("{{ route('assignment.check-availability') }}", { start_date: start, end_date: end }, function(res) {
                $('.petugas-row').each(function() {
                    const id = parseInt($(this).data('id'));
                    $('#status_busy_' + id).toggleClass('d-none', !(res.busy_users && res.busy_users.includes(id)));
                });
            });
        }
    }

    // --- INITIALIZE ---
    $('#activity_type_id').trigger('change');

    $('input[name="approval_type"]').on('change', function() {
        if ($(this).val() === 'multiple') {
            $('#reviewer-container').slideDown();
            $('#reviewer_id').prop('required', true);
        } else {
            $('#reviewer-container').slideUp();
            $('#reviewer_id').prop('required', false);
        }
    });
});
</script>
@endsection