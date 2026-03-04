@extends('layouts.app')

@section('content')
<style>
    :root {
        --bps-blue: #0058a8;
        --bps-sky: #eef6ff;
    }

    .guide-header {
        background: linear-gradient(135deg, var(--bps-blue) 0%, #007bff 100%);
        border-radius: 25px;
        padding: 3rem 2rem;
        color: white;
        margin-bottom: 2.5rem;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 88, 168, 0.15);
    }

    .guide-card {
        border: none;
        border-radius: 20px;
        transition: all 0.3s ease;
        height: 100%;
        background: white;
        border: 1px solid #f1f5f9;
    }

    .guide-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        border-color: var(--bps-blue);
    }

    .guide-icon {
        width: 60px;
        height: 60px;
        background: var(--bps-sky);
        color: var(--bps-blue);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .step-number {
        width: 28px;
        height: 28px;
        background: var(--bps-blue);
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        margin-right: 10px;
    }

    .accordion-button:not(.collapsed) {
        background-color: var(--bps-sky);
        color: var(--bps-blue);
        font-weight: bold;
    }

    .accordion-item {
        border-radius: 15px !important;
        border: 1px solid #f1f5f9 !important;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .role-tag {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
    }
</style>

<div class="container-fluid px-4 pb-5">
    <div class="guide-header">
        <h1 class="fw-bold mb-2">Pusat Bantuan & Panduan</h1>
        <p class="opacity-75 mb-0 font-medium">Pelajari alur kerja sistem Harmoni BPS Kabupaten Tuban.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card guide-card p-4 text-center">
                <div class="guide-icon mx-auto"><i class="fas fa-briefcase"></i></div>
                <h5 class="fw-bold text-dark">Tugas Lapangan</h5>
                <p class="text-muted small">Alur pelaporan kegiatan pengawasan lapangan mulai dari penugasan hingga rekap PDF.</p>
                <a href="#faqAccordion" class="btn btn-link p-0 text-primary fw-bold text-decoration-none small">Baca Selengkapnya</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card guide-card p-4 text-center">
                <div class="guide-icon mx-auto"><i class="fas fa-handshake"></i></div>
                <h5 class="fw-bold text-dark">Agenda Rapat</h5>
                <p class="text-muted small">Panduan presensi digital dengan tanda tangan dan pengisian notulensi rapat.</p>
                <a href="#faqAccordion" class="btn btn-link p-0 text-primary fw-bold text-decoration-none small">Baca Selengkapnya</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card guide-card p-4 text-center">
                <div class="guide-icon mx-auto"><i class="fas fa-bell"></i></div>
                <h5 class="fw-bold text-dark">Sistem Notifikasi</h5>
                <p class="text-muted small">Memahami arti angka notifikasi merah dan kuning pada sidebar aplikasi.</p>
                <a href="#faqAccordion" class="btn btn-link p-0 text-primary fw-bold text-decoration-none small">Baca Selengkapnya</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="fas fa-book-open text-primary me-3"></i>Pertanyaan & Panduan Penggunaan
            </h4>

            <div class="accordion" id="faqAccordion">
                {{-- FAQ 1: Plotting Tugas --}}
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            <span class="step-number">1</span> Bagaimana cara melakukan Plotting Penugasan?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            <span class="badge role-tag bg-danger mb-2">Admin & Katim</span>
                            <ol>
                                <li>Pilih menu <strong>Manajemen User > Plotting Tugas</strong>.</li>
                                <li>Pilih <strong>Jenis Kegiatan</strong> (Lapangan atau Rapat).</li>
                                <li>Jika memilih <strong>Rapat</strong>, sistem akan otomatis menyederhanakan inputan menjadi satu tanggal pelaksanaan saja.</li>
                                <li>Gunakan fitur <strong>Smart Validation</strong> (nama petugas yang berwarna merah/tercoret berarti sudah memiliki agenda lain di tanggal tersebut).</li>
                                <li>Klik <strong>Konfirmasi & Kirim</strong>. Anggota akan mendapatkan notifikasi di sidebar mereka.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- FAQ 2: Pelaporan Lapangan --}}
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                            <span class="step-number">2</span> Bagaimana cara melaporkan Tugas Lapangan?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            <span class="badge role-tag bg-success mb-2">Seluruh Pegawai</span>
                            <ol>
                                <li>Lihat menu <strong>Tugas Lapangan > Daftar Tugas</strong>. Perhatikan angka notifikasi merah.</li>
                                <li>Klik tombol <strong>Lapor</strong> pada baris tugas yang ingin diselesaikan.</li>
                                <li>Isi format aktivitas, permasalahan, dan solusi. Gunakan format angka (1. , 2.) agar hasil PDF rapi dan sejajar.</li>
                                <li>Unggah foto dokumentasi (bisa lebih dari satu foto).</li>
                                <li>Klik <strong>Kirim Laporan</strong>. Status akan berubah menjadi "Selesai" dan angka notifikasi akan berkurang.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- FAQ 3: Presensi Rapat --}}
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                            <span class="step-number">3</span> Bagaimana cara Absensi Rapat Digital?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            <p>Setiap ada jadwal rapat, akan muncul notifikasi kuning di menu <strong>Agenda Rapat > Jadwal Rapat</strong>.</p>
                            <ol>
                                <li>Klik tombol <strong>Absen</strong> pada agenda rapat hari ini.</li>
                                <li>Goreskan tanda tangan digital Anda pada layar yang tersedia.</li>
                                <li>Klik <strong>Kirim Kehadiran</strong>. Sistem akan otomatis mengalihkan Anda kembali ke daftar rapat dalam 3 detik.</li>
                                <li>Status kehadiran Anda akan berubah menjadi <span class="text-success fw-bold">Hadir</span>.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- FAQ 4: Rekap & Download --}}
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                            <span class="step-number">4</span> Dimana saya bisa mengunduh rekap laporan PDF?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            <p>Untuk tugas lapangan, masuk ke menu <strong>Riwayat Laporan</strong>. Gunakan tombol ikon PDF pada kolom aksi untuk mengunduh laporan per individu kegiatan.</p>
                            <p>Untuk dokumentasi foto rapat, Anda bisa masuk ke <strong>Detail Riwayat Rapat</strong> dan mengunduh foto satu per satu dengan mengarahkan kursor/hover pada foto.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-light border-0 rounded-4 p-4 mt-5 text-center">
                <h6 class="fw-bold mb-2">Butuh Bantuan Lebih Lanjut?</h6>
                <p class="text-muted small mb-3">Jika terjadi kendala teknis pada sistem Harmoni, silakan hubungi tim IT.</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="https://wa.me/628x" class="btn btn-bps-blue text-white px-4 rounded-pill small fw-bold" style="background: var(--bps-blue);">
                        <i class="fab fa-whatsapp me-2"></i>Hubungi Admin
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 rounded-pill small fw-bold">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5 mb-4 text-muted small">
        <p>© 2026 Harmoni BPS Tuban - Versi 2.0 (Updated Content)</p>
    </div>
</div>
@endsection