@extends('layouts.app')

@section('content')
<style>
    /* Merah tegas untuk bintang required */
    .text-danger { color: #dc3545 !important; font-weight: bold; }
    .form-label span.required { color: #dc3545; margin-left: 2px; }
    /* Warna Biru BPS untuk header dan button */
    .bg-bps-blue { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); }
    .btn-bps-primary { background: #0058a8; color: white; transition: 0.3s; border: none; }
    .btn-bps-primary:hover { background: #004481; color: white; transform: translateY(-2px); }
</style>

<div class="container-fluid">
    @php
        // 1. Pecah string Lokasi
        $currentLocation = $agenda->location;
        $currentDesa = '';
        $currentKec = '';
        if (str_contains($currentLocation, ', Kec. ')) {
            $parts = explode(', Kec. ', $currentLocation);
            $currentKec = trim($parts[1] ?? '');
            $currentDesa = trim(str_replace('Desa ', '', $parts[0] ?? ''));
        }

        // 2. LOGIKA TANGGAL PELAKSANAAN
        $tanggalTerdeteksi = $agenda->tanggal_pelaksanaan 
                             ?? ($agenda->event_date 
                             ?? now()->format('Y-m-d'));

        $valTanggal = \Carbon\Carbon::parse($tanggalTerdeteksi)->format('Y-m-d');

        // 3. DATA VALIDASI UNTUK JS
        $userCuti = \App\Models\Absensi::where('user_id', Auth::id())
                ->whereIn('status', ['CT', 'CST1']) // Gunakan whereIn untuk menangkap semua jenis cuti
                ->get(['start_date', 'end_date', 'status']);
    
        $laporanTerpakai = \App\Models\Agenda::where('assigned_to', Auth::id())
                ->where('id', '!=', $agenda->id) 
                ->whereNotNull('tanggal_pelaksanaan')
                ->where('status_laporan', 'Selesai')
                ->pluck('tanggal_pelaksanaan')
                ->toArray();
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-11 mt-4">
            {{-- Header --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-bps-blue p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-file-signature fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Input Laporan Pengawasan Lapangan</h5>
                            <small class="text-white text-opacity-75">Silakan lengkapi data hasil temuan Anda di lapangan</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-primary rounded-pill px-3 shadow-sm fw-bold">FORM LAPORAN</span>
                </div>
            </div>

            {{-- Pastikan Route-nya ke TASK STORE bukan HISTORY UPDATE jika ini input baru --}}
            <form id="formLaporan" action="{{ route('task.store', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- SISI KIRI --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 text-muted border-bottom pb-2"><i class="fas fa-lock me-2"></i>Informasi Baku</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan</label>
                                <textarea class="form-control border-0 bg-white fw-bold rounded-3" rows="2" readonly style="resize: none;">{{ $agenda->title }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor Surat Tugas</label>
                                <input type="text" class="form-control border-0 bg-white fw-bold rounded-3 text-primary" value="{{ $agenda->nomor_surat_tugas ?? '-' }}" readonly>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-primary">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Lokasi Pengawasan</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan <span class="text-danger">*</span></label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-3 border-0 bg-light p-3 fw-bold shadow-sm" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach(["Bancar", "Bangilan", "Grabagan", "Jatirogo", "Jenu", "Kenduruan", "Kerek", "Merakurak", "Montong", "Palang", "Parengan", "Plumpang", "Rengel", "Semanding", "Senori", "Singgahan", "Soko", "Tambakboyo", "Tuban", "Widang"] as $kec)
                                        <option value="{{ $kec }}" {{ (old('kecamatan', $currentKec) == $kec) ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Desa / Kelurahan <span class="text-danger">*</span></label>
                                <select name="desa" id="desa" class="form-select rounded-3 border-0 bg-light p-3 fw-bold shadow-sm" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-info">
                            <h6 class="fw-bold mb-3 text-info"><i class="fas fa-calendar-check me-2"></i>Waktu & Foto</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Tanggal Pelaksanaan Lapangan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" class="form-control rounded-3 shadow-sm border-info fw-bold" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       value="{{ $valTanggal }}" required>
                            </div>
                            <div class="mb-0">
                                {{-- BINTANG MERAH DITAMBAHKAN DISINI --}}
                                <label class="form-label small fw-bold text-dark text-uppercase">Foto Dokumentasi <span class="text-danger">*</span></label>
                                <input type="file" name="fotos[]" id="foto_upload" class="form-control" accept="image/*" multiple required>
                                <div class="form-text text-danger fw-bold" style="font-size: 0.65rem;">
                                    <i class="fas fa-info-circle me-1"></i> Format: JPG/PNG. Maksimal 10MB per foto.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark"><i class="fas fa-clipboard-check me-2 text-success"></i>Detail Hasil Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">RESPONDEN / PETUGAS DITEMUI <span class="text-danger">*</span></label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" placeholder="Contoh: Bapak Ahmad" required value="{{ old('responden', $agenda->responden) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS DILAKUKAN <span class="text-danger">*</span></label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="6" placeholder="Jelaskan aktivitas Anda..." required>{{ old('aktivitas', $agenda->aktivitas) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN LAPANGAN <span class="text-danger">*</span></label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="3" placeholder="Kendala di lapangan..." required>{{ old('permasalahan', $agenda->permasalahan) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success text-uppercase">Solusi / Tindak Lanjut <span class="text-danger">*</span></label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="3" placeholder="Saran/Tindak lanjut..." required>{{ old('solusi_antisipasi', $agenda->solusi_antisipasi) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('task.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">Batal</a>
                                <button type="submit" class="btn btn-bps-primary px-5 rounded-pill fw-bold shadow-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const daftarCuti = @json($userCuti);
    const laporanTerpakai = @json($laporanTerpakai);

    document.getElementById('tanggal_pelaksanaan').addEventListener('change', function() {
        const tglTerpilih = this.value; // Format: YYYY-MM-DD
        
        if (!tglTerpilih) return;

        // --- A. LOGIKA CEK CUTI (DIPERKETAT) ---
        let isCuti = false;
        let rangeCutiText = "";

        daftarCuti.forEach(range => {
            // Pastikan format date-only (ambil 10 karakter pertama YYYY-MM-DD)
            const start = range.start_date.substring(0, 10);
            const end = range.end_date.substring(0, 10);

            if (tglTerpilih >= start && tglTerpilih <= end) {
                isCuti = true;
                rangeCutiText = `${start} s.d ${end}`;
            }
        });

        if (isCuti) {
            Swal.fire({
                title: 'Akses Ditolak!',
                html: `Anda tidak bisa memilih tanggal <b>${tglTerpilih}</b> karena status Anda sedang <b>CUTI</b> pada periode ${rangeCutiText}.`,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            this.value = ''; // Kosongkan kembali
            return; // Berhenti disini
        }

        // --- B. LOGIKA CEK TANGGAL SUDAH TERPAKAI ---
        // Normalisasi array laporanTerpakai (ambil YYYY-MM-DD saja)
        const cleanLaporanTerpakai = laporanTerpakai.map(tgl => tgl.substring(0, 10));

        if (cleanLaporanTerpakai.includes(tglTerpilih)) {
            Swal.fire({
                title: 'Jadwal Bentrok!',
                text: 'Tanggal tersebut sudah Anda gunakan untuk melaporkan penugasan lain. Silakan pilih tanggal pelaksanaan yang berbeda.',
                icon: 'warning',
                confirmButtonColor: '#f59e0b'
            });
            this.value = ''; // Kosongkan kembali
            return;
        }
    });

    // --- LOGIKA KECAMATAN & DESA (TETAP SAMA) ---
    const dataWilayah = {
        "Bancar": ["Bancar", "Banjarejo", "Bogorejo", "Bulujowo", "Demit", "Gandu", "Jatisari", "Karangrejo", "Kayen", "Luwihaji", "Margosuko", "Ngadipuro", "Ngujuran", "Pugoh", "Sembungin", "Sidotentrem", "Siruar", "Sukasari", "Sumberan", "Tlogoagung", "Tengger Kulon", "Tengger Wetan"],
        "Bangilan": ["Bangilan", "Banjarworo", "Bate", "Bedukan", "Kumpulrejo", "Ngroto", "Sidokumpul", "Sidotentrem", "Sidorejo", "Soto", "Wedi", "Klakeh", "Kebonagung", "Wediyani"],
        "Grabagan": ["Grabagan", "Banyubang", "Dahor", "Dermawuharjo", "Gesikan", "Menyunyur", "Ngasinan", "Ngandong", "Ngarum", "Pacing", "Pakis", "Waleran"],
        "Jatirogo": ["Jatirogo", "Badegan", "Besowo", "Dingin", "Jatirejo", "Karangtengah", "Kebonharjo", "Ketitang", "Klampok", "Paseyan", "Sadang", "Sekaran", "Sidotentrem", "Sugihan", "Wotsogo"],
        "Jenu": ["Jenu", "Beji", "Jenggolo", "Kaliuntu", "Karangasem", "Mentoso", "Purworejo", "Rawasan", "Remen", "Sekardadi", "Socorejo", "Suwalan", "Tasikharjo", "Temaji", "Wadang", "Sugiawaras", "Sumurgeneng"],
        "Kenduruan": ["Kenduruan", "Bendonglateng", "Jamprong", "Jatihadi", "Pandan Agung", "Pandanwangi", "Papringan", "Sidorejo", "Sidomukti"],
        "Kerek": ["Kerek", "Gaji", "Gemulung", "Hargoretno", "Jarorejo", "Karanglo", "Kasiman", "Kedungrejo", "Margomulyo", "Mliwang", "Padasan", "Sidonganti", "Sumberarum", "Temayang", "Trantang", "Wolo"],
        "Merakurak": ["Merakurak", "Bogorejo", "Borehbilo", "Kapu", "Mandirejo", "Paparuan", "Sambonggede", "Sidoasri", "Sengon", "Sumberejo", "Tahulu", "Tegalrejo", "Temandang", "Tuwiri Kulon", "Tuwiri Wetan"],
        "Montong": ["Montong", "Bringin", "Guwoterus", "Jetak", "Maindu", "Manjung", "Montongsekar", "Nguluhan", "Pacing", "Pakel", "Pucangan", "Sumurgung", "Talangkembar", "Talun"],
        "Palang": ["Palang", "Cendoro", "Cepokorejo", "Dawung", "Glagahwaru", "Karangagung", "Ketambul", "Kradenan", "Leran Kulon", "Leran Wetan", "Ngimbang", "Panyuran", "Sumurgung", "Tegalbang", "Tasikmadu", "Waru"],
        "Parengan": ["Parengan", "Brangkal", "Cengkong", "Dagangan", "Kemlaten", "Kumpulrejo", "Mergoasri", "Mojoagung", "Mulyoagung", "Mulyorejo", "Ngawun", "Pacing", "Parangbatu", "Selogabus", "Sembung", "Suciharjo", "Sugihwaras", "Sukorejo", "Tinggahan"],
        "Plumpang": ["Plumpang", "Bandungrejo", "Cangkring", "Kebomlati", "Kecapi", "Kedungasri", "Kedungrejo", "Kedungsoko", "Kepohagung", "Klapadyangan", "Magersari", "Ngadipuro", "Panyuran", "Penidon", "Plandirejo", "Sembungrejo", "Sumberejo", "Trutup"],
        "Rengel": ["Rengel", "Banjaragung", "Bulurejo", "Campurejo", "Kanor Kulon", "Karangtinoto", "Kebonagung", "Maibit", "Ngadirejo", "Pekuwon", "Prambontergayang", "Punggulrejo", "Rengel", "Sawahan", "Sumberejo", "Tambakharjo"],
        "Semanding": ["Semanding", "Bejagung", "Genaharjo", "Gesing", "Jadi", "Karang", "Kowang", "Ngino", "Penambangan", "Prunggahan Kulon", "Prunggahan Wetan", "Sambongrejo", "Semanding", "Tegalagung", "Tunah"],
        "Senori": ["Senori", "Banyuurip", "Jatisari", "Kaligede", "Kerep", "Leran", "Meduri", "Rayung", "Sendang", "Sidoharjo", "Wanglukulon", "Wangluwetan"],
        "Singgahan": ["Singgahan", "Binangun", "Lajo Kidul", "Lajo Lor", "Mulyoasri", "Mulyorejo", "Ngawun", "Saren", "Tanjungrejo", "Tingkis", "Tunggulrejo"],
        "Soko": ["Soko", "Bangunrejo", "Cekalang", "Glodog", "Jati", "Jegulo", "Kandangan", "Kenongosari", "Klumpit", "Menilo", "Nguruan", "Pandansari", "Pandanagung", "Prambontergayang", "Sandingrowo", "Simo", "Soko", "Tandun", "Tlogowaru"],
        "Tambakboyo": ["Tambakboyo", "Belikanget", "Cokrowati", "Dikir", "Gadun", "Kalisari", "Kenanti", "Klutuk", "Mabulur", "Nguluhan", "Pabeyan", "Plajan", "Pulogede", "Sawir", "Sotang", "Sukoharjo", "Tambakboyo"],
        "Tuban": ["Banyuurip", "Doromukti", "Gedongombo", "Karang", "Karangsari", "Kebonsari", "Kutorejo", "Latsari", "Mondokan", "Panyuran", "Perbon", "Ronggomulyo", "Sendangharjo", "Sidomulyo", "Sukolilo", "Sukolilo", "Sugihwaras", "Sumurgung"],
        "Widang": ["Widang", "Banjar", "Bunut", "Kompang", "Mulyorejo", "Ngadirejo", "Ngadipuro", "Patihan", "Simorejo", "Sumberejo", "Tegalrejo", "Tegalsari", "Widang"]
    };

    const kecSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');
    const initialDesa = @json(old('desa', $currentDesa));

    function updateDesaOptions(selectedKec, preselectDesa = '') {
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (selectedKec && dataWilayah[selectedKec]) {
            desaSelect.disabled = false;
            dataWilayah[selectedKec].sort().forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.text = desa;
                if (preselectDesa === desa) { option.selected = true; }
                desaSelect.add(option);
            });
        } else {
            desaSelect.disabled = true;
        }
    }

    if (kecSelect.value) { updateDesaOptions(kecSelect.value, initialDesa); }
    kecSelect.addEventListener('change', function() { updateDesaOptions(this.value); });
</script>
@endsection