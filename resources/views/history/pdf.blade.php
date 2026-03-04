<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perjalanan Dinas - BPS Tuban</title>
    <style>
        /* Standar Dokumen Dinas A4 */
        @page { margin: 1.5cm 2cm; }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 12px; 
            line-height: 1.5; 
            color: #111; 
        }

        /* Kop Surat BPS */
        .kop-surat { border-bottom: 2.5px solid #000; padding-bottom: 10px; margin-bottom: 15px; width: 100%; }
        .logo-bps { width: 70px; float: left; margin-top: 5px; }
        .instansi-info { text-align: center; margin-right: 70px; }
        .instansi-info h2 { margin: 0; font-size: 16px; text-transform: uppercase; color: #000; }
        .instansi-info h3 { margin: 0; font-size: 14px; text-transform: uppercase; color: #000; }
        .instansi-info p { margin: 0; font-size: 10px; font-style: italic; color: #555; }

        .judul-laporan { 
            text-align: center; 
            font-weight: bold; 
            margin: 20px 0; 
            font-size: 14px; 
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* Tabel Laporan */
        .table-laporan { width: 100%; border-collapse: collapse; }
        .table-laporan td { 
            padding: 5px 2px; 
            vertical-align: top; 
        }
        
        .label { 
            width: 30%; 
            font-weight: bold; 
            color: #333; 
            text-transform: uppercase;
            font-size: 11px;
        }
        .separator { width: 3%; text-align: center; font-weight: bold; }
        .content { width: 67%; text-align: justify; color: #000; }

        /* Style Khusus Penomoran Sejajar */
        .report-list {
            margin: 0;
            padding-left: 18px; /* Memberi ruang untuk nomor */
            list-style-position: outside; /* Angka di luar blok teks */
        }
        .report-list li {
            margin-bottom: 5px;
            text-align: justify;
        }

        /* Tanda Tangan */
        .signature-wrapper { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 220px; text-align: center; line-height: 1.2; }
        
        .page-break { page-break-after: always; }

        /* Dokumentasi */
        .photo-header { 
            text-align: center; 
            font-weight: bold; 
            margin-bottom: 15px; 
            font-size: 13px; 
            border-bottom: 1px solid #000; 
            padding-bottom: 5px; 
        }
        .photo-table { width: 100%; }
        .photo-td { width: 50%; padding: 10px; text-align: center; }
        .photo-td img { 
            width: 100%; 
            max-height: 220px; 
            object-fit: cover; 
            border: 1px solid #ddd;
        }
        .photo-caption { font-size: 9px; color: #666; margin-top: 5px; }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    {{-- Logika Helper untuk Merapikan List Nomor --}}
    @php
        function formatNumberedList($text) {
            // Cek jika teks mengandung pola angka (1. , 2. dst)
            if (preg_match('/^\d+\./m', $text)) {
                // Hilangkan angka di depan teks manual karena diganti oleh HTML list
                $cleanText = preg_replace('/^\d+\.\s*/m', '<li>', $text);
                return '<ol class="report-list">' . str_replace("\n", "</li>", $cleanText) . '</li></ol>';
            }
            // Jika tidak ada nomor, tampilkan teks biasa dengan baris baru
            return nl2br(e($text));
        }
    @endphp

    <div class="kop-surat">
        <img src="{{ public_path('img/logo-bps.png') }}" class="logo-bps">
        <div class="instansi-info">
            <h2>BADAN PUSAT STATISTIK</h2>
            <h3>KABUPATEN TUBAN</h3>
            <p>Jalan Raya Manunggal No. 8 Sukolilo, Panyuran, Kec. Tuban, Kabupaten Tuban, Jawa Timur</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="judul-laporan">LAPORAN PERJALANAN DINAS</div>

    <table class="table-laporan">
        <tr>
            <td class="label">Nama Kegiatan</td>
            <td class="separator">:</td>
            <td class="content"><strong>{{ $agenda->title }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Pegawai</td>
            <td class="separator">:</td>
            <td class="content">{{ $agenda->assignee->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Surat Tugas</td>
            <td class="separator">:</td>
            <td class="content">{{ $agenda->nomor_surat_tugas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tujuan / Lokasi</td>
            <td class="separator">:</td>
            <td class="content">{{ $agenda->location }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Pelaksanaan</td>
            <td class="separator">:</td>
            <td class="content">{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Responden</td>
            <td class="separator">:</td>
            <td class="content">{{ $agenda->responden ?? '-' }}</td>
        </tr>

        {{-- BAGIAN LAPORAN DENGAN AUTO-LIST --}}
        <tr>
            <td class="label">Aktivitas yang Dilakukan</td>
            <td class="separator">:</td>
            <td class="content">{!! formatNumberedList($agenda->aktivitas) !!}</td>
        </tr>
        <tr>
            <td class="label">Permasalahan ditemui</td>
            <td class="separator">:</td>
            <td class="content">{!! formatNumberedList($agenda->permasalahan) !!}</td>
        </tr>
        <tr>
            <td class="label">Solusi / Langkah Antisipatif</td>
            <td class="separator">:</td>
            <td class="content">{!! formatNumberedList($agenda->solusi_antisipasi) !!}</td>
        </tr>
    </table>

    <div class="signature-wrapper">
        <div class="signature-box">
            Tuban, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Pegawai,<br><br><br><br><br>
            <strong>{{ $agenda->assignee->nama_lengkap }}</strong><br>
            NIP. {{ $agenda->assignee->nip ?? '..........................' }}
        </div>
        <div class="clear"></div>
    </div>

    @if($agenda->photos->count() > 0)
    <div class="page-break"></div>
    <div class="photo-header">DOKUMENTASI KEGIATAN</div>
    
    <table class="photo-table">
        @foreach($agenda->photos->chunk(2) as $chunk)
        <tr>
            @foreach($chunk as $p)
            <td class="photo-td">
                <img src="{{ public_path('storage/' . $p->photo_path) }}">
                <div class="photo-caption">Gbr: Dokumentasi Lapangan</div>
            </td>
            @endforeach
            @if($chunk->count() == 1) <td style="width:50%;"></td> @endif
        </tr>
        @endforeach
    </table>
    @endif

</body>
</html>