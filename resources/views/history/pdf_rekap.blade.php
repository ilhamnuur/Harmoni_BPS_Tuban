<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Reset & Base */
        @page { margin: 0.5cm 1cm 1cm 1cm; } /* Perkecil margin atas agar tidak kosong */
        body { font-family: sans-serif; font-size: 8.5pt; color: #333; line-height: 1.2; margin: 0; }

        /* Header Layout menggunakan Table agar stabil */
        .kop-table { width: 100%; border: none; margin-bottom: 10px; border-bottom: 2px solid #000; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        
        .header-text { text-align: center; }
        .header-text h1 { font-size: 12pt; margin: 0; }
        .header-text h2 { font-size: 11pt; margin: 2px 0; }
        .header-text p { font-size: 9pt; margin: 0; color: #0056b3; font-weight: bold; }
        
        .meta { margin-bottom: 10px; font-size: 8pt; border-bottom: 1px solid #eee; padding-bottom: 5px; }

        /* Table Data */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; /* Kunci lebar kolom */
        }
        
        /* Hilangkan Page Break Avoid agar baris bisa terpotong dan tidak loncat halaman */
        table.data-table, .data-table tr, .data-table td {
            page-break-inside: auto !important;
        }

        .data-table th { 
            background-color: #f8f9fa; 
            border: 1px solid #333; 
            padding: 6px; 
            font-size: 8pt;
            text-align: center;
        }

        .data-table td { 
            border: 1px solid #333; 
            padding: 8px; 
            vertical-align: top; 
            word-wrap: break-word; 
        }

        .bg-gray { background-color: #fafafa; }
        .label { font-weight: bold; color: #555; font-size: 7.5pt; display: block; margin-bottom: 2px; text-decoration: underline; }
        .text-center { text-align: center; }

        /* Tanda Tangan tanpa Float */
        .footer-wrapper { margin-top: 20px; width: 100%; }
        .footer-sign { width: 200px; text-align: center; margin-left: auto; } /* margin-left: auto menggantikan float:right */
    </style>
</head>
<body>

    @php \Carbon\Carbon::setLocale('id'); @endphp

    <table class="kop-table">
        <tr>
            <td width="60">
                <img src="{{ public_path('img/logo-bps.png') }}" width="60">
            </td>
            <td class="header-text">
                <h1>BADAN PUSAT STATISTIK</h1>
                <h2>KABUPATEN TUBAN</h2>
                <p>LAPORAN SEMUA KEGIATAN PENGAWASAN LAPANGAN</p>
            </td>
            <td width="60"></td> </tr>
    </table>

    <div class="meta">
        Digenerate: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} &bull; Total Laporan: {{ $riwayat->count() }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Pegawai & Tanggal</th>
                <th width="30%">Kegiatan & Lokasi</th>
                <th width="45%">Detail Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $key => $l)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>
                    <strong>{{ $l->assignee->nama_lengkap }}</strong><br>
                    <span style="font-size: 7.5pt;">{{ \Carbon\Carbon::parse($l->event_date)->translatedFormat('l, d/m/Y') }}</span>
                </td>
                <td>
                    <strong>{{ $l->title }}</strong><br>
                    <small>Lokasi: {{ $l->location }}</small>
                </td>
                <td class="bg-gray">
                    <span class="label">AKTIVITAS:</span>
                    <div style="margin-bottom: 8px;">{{ $l->aktivitas }}</div>
                    
                    <span class="label">PERMASALAHAN:</span>
                    <div>{{ $l->permasalahan }}</div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-wrapper">
        <div class="footer-sign">
            <p>Tuban, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <br><br><br>
            <p><strong>( ________________ )</strong></p>
            <p>Kepala BPS Tuban</p>
        </div>
    </div>

</body>
</html>             