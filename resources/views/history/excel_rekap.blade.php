@php
    \Carbon\Carbon::setLocale('id');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Gaya Judul Utama */
        .title { font-size: 16pt; font-weight: bold; text-align: center; color: #000000; }
        .meta { font-size: 10pt; text-align: center; color: #333; }
        
        /* Header Tabel */
        .table-header { 
            background-color: #0070c0; 
            color: #ffffff; 
            font-weight: bold; 
            text-align: center; 
            border: 2pt solid #000000; 
            vertical-align: middle;
        }

        /* Class untuk Teks Panjang (Nama, Aktivitas, dll) */
        .cell-data { 
            border: 1pt solid #000000; 
            vertical-align: top; /* Rata Atas */
            padding: 5px;
        }

        /* Class untuk Angka & Tanggal (BENAHI DI SINI) */
        .text-center { 
            text-align: center; 
            vertical-align: top; /* TAMBAHKAN INI BIAR GAK DI BAWAH */
            border: 1pt solid #000000;
            padding: 5px;
        }

        /* Background Zebra */
        .bg-odd { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="12" class="title">BADAN PUSAT STATISTIK KABUPATEN TUBAN</th>
        </tr>
        <tr>
            <th colspan="12" class="title" style="font-size: 12pt;">LAPORAN KEGIATAN PENGAWASAN LAPANGAN</th>
        </tr>
        <tr>
            <th colspan="12" class="meta">Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</th>
        </tr>
        <tr></tr>

        <thead>
            <tr>
                <th class="table-header">No</th>
                <th class="table-header">Nama Pegawai</th>
                <th class="table-header">Nomor Surat Tugas</th>
                <th class="table-header">Jenis Kegiatan</th>
                <th class="table-header">Tujuan & Lokasi</th>
                <th class="table-header">Tanggal</th>
                <th class="table-header">Hari</th>
                <th class="table-header">Aktivitas</th>
                <th class="table-header">Permasalahan</th>
                <th class="table-header">Responden</th>
                <th class="table-header">Solusi</th>
                <th class="table-header">Input Sistem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $key => $l)
            <tr class="{{ $key % 2 == 0 ? '' : 'bg-odd' }}">
                {{-- Semua kolom sekarang pakai vertical-align: top via class masing-masing --}}
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="cell-data">{{ $l->assignee->nama_lengkap }}</td>
                <td class="cell-data">{{ $l->nomor_surat_tugas }}</td>
                <td class="text-center">{{ $l->activityType->name ?? '-' }}</td>
                <td class="cell-data">{{ $l->title }} - {{ $l->location }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($l->event_date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($l->event_date)->translatedFormat('l') }}</td>
                <td class="cell-data">{{ $l->aktivitas }}</td>
                <td class="cell-data">{{ $l->permasalahan }}</td>
                <td class="cell-data">{{ $l->responden }}</td>
                <td class="cell-data">{{ $l->solusi_antisipasi }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($l->updated_at)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>