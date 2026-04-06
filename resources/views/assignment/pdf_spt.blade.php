<!DOCTYPE html>
<html>
<head>
    <title>{{ ($agenda->activity_type_id == 1) ? 'SPT' : 'Memorandum' }} - {{ $agenda->nomor_surat_tugas }}</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10.5pt; line-height: 1.5; color: #000; }
        
        /* HEADER CENTERED */
        .header { text-align: center; margin-bottom: 20px; width: 100%; }
        .logo-bps { width: 65px; display: block; margin: 0 auto 5px auto; }
        .kop-text { font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-bps { font-size: 12pt; }
        .kop-kab { font-size: 13pt; }
        
        .title-section { text-align: center; margin-top: 10px; margin-bottom: 20px; }
        .title-spt { font-size: 11pt; font-weight: bold; text-decoration: underline; }
        
        /* TABLE MAIN FIX */
        .table-main { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px; 
            table-layout: fixed; 
        }
        .table-main td { 
            vertical-align: top; 
            padding: 4px 0; 
            word-wrap: break-word; 
        }

        .col-label { width: 18%; } 
        .col-sep   { width: 3%; text-align: center; } 
        .col-isi   { width: 79%; text-align: justify; }

        .list-poin { margin: 0; padding-left: 17px; }
        .list-poin li { margin-bottom: 3px; }

        /* FOOTER TTD */
        .footer { margin-top: 30px; width: 100%; }
        .ttd-wrapper { float: right; width: 260px; text-align: center; }
        .ttd-image { height: 80px; margin: 5px 0; }
        
        /* TABLE KHUSUS MEMO */
        .table-memo { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-top: 10px; }
        .table-memo th, .table-memo td { border: 1px solid #000; padding: 8px; text-align: center; font-size: 10pt; }
        .table-memo th { background-color: #f2f2f2; }

        .page-break { page-break-after: always; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>

{{-- ========================================================== --}}
{{-- MODE SPT: HANYA TUGAS LAPANGAN (1) --}}
{{-- ========================================================== --}}
@if($agenda->activity_type_id == 1)
    @if($mode == 'perorang')
        @foreach($grupPetugas as $index => $p)
            <div class="header">
                <img src="{{ public_path('img/logo-bps.png') }}" class="logo-bps">
                <div class="kop-text">
                    <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
                    <span class="kop-kab">KABUPATEN TUBAN</span>
                </div>
            </div>

            <div class="title-section">
                <span class="title-spt uppercase">SURAT PERINTAH TUGAS</span><br>
                <span class="bold uppercase">NOMOR : {{ $agenda->nomor_surat_tugas }}</span>
            </div>

            <table class="table-main">
                <tr>
                    <td class="col-label">Menimbang</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">
                        @if($agenda->menimbang)
                            {!! nl2br(e($agenda->menimbang)) !!}
                        @else
                            bahwa untuk kelancaran pelaksanaan kegiatan {{ $agenda->title }}, maka dipandang perlu untuk menugaskan pegawai yang namanya tersebut di bawah ini;
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="col-label">Mengingat</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">
                        @if($agenda->mengingat)
                            {!! nl2br(e($agenda->mengingat)) !!}
                        @else
                            <ol class="list-poin">
                                <li>Undang-Undang Nomor 16 Tahun 1997 tentang Statistik;</li>
                                <li>Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;</li>
                                <li>Peraturan Presiden Nomor 86 Tahun 2007 tentang Badan Pusat Statistik;</li>
                            </ol>
                        @endif
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin: 15px 0;" class="bold">Memberi Tugas / Perintah:</div>

            <table class="table-main">
                <tr>
                    <td class="col-label">Kepada</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi bold">
                        {{ $p->assignee->nama_lengkap }}<br>
                        <span style="font-weight: normal;">NIP. {{ $p->assignee->nip ?? '-' }} / {{ $p->assignee->role }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="col-label">Untuk</td>
                    <td class="col-sep">:</td>
                    <td class="col-isi">Melaksanakan tugas <strong>{{ $agenda->title }}</strong> di Kabupaten Tuban pada tanggal <strong>{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y') }}</strong>.</td>
                </tr>
            </table>

            <div class="footer">
                <div class="ttd-wrapper">
                    Tuban, {{ now()->translatedFormat('d F Y') }}<br>
                    Kepala BPS Kabupaten Tuban,<br>
                    <div style="height: 85px;">
                        @if($agenda->status_approval === 'Approved' && $agenda->approver->signature)
                            <img src="{{ public_path('storage/' . $agenda->approver->signature) }}" class="ttd-image">
                        @endif
                    </div>
                    <span class="bold" style="text-decoration: underline;">{{ $agenda->approver->nama_lengkap }}</span><br>
                    NIP. {{ $agenda->approver->nip }}
                </div>
            </div>
            @if(!$loop->last) <div class="page-break"></div> @endif
        @endforeach
    @else
        <div class="header">
            <img src="{{ public_path('img/logo-bps.png') }}" class="logo-bps">
            <div class="kop-text">
                <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
                <span class="kop-kab">KABUPATEN TUBAN</span>
            </div>
        </div>

        <div class="title-section">
            <span class="title-spt uppercase">SURAT PERINTAH TUGAS</span><br>
            <span class="bold uppercase">NOMOR : {{ $agenda->nomor_surat_tugas }}</span>
        </div>

        <table class="table-main">
            <tr>
                <td class="col-label">Menimbang</td>
                <td class="col-sep">:</td>
                <td class="col-isi">
                    @if($agenda->menimbang)
                        {!! nl2br(e($agenda->menimbang)) !!}
                    @else
                        bahwa untuk kelancaran pelaksanaan kegiatan {{ $agenda->title }}, maka dipandang perlu untuk menugaskan pegawai yang namanya tersebut di bawah ini;
                    @endif
                </td>
            </tr>
            <tr>
                <td class="col-label">Mengingat</td>
                <td class="col-sep">:</td>
                <td class="col-isi">
                    @if($agenda->mengingat)
                        {!! nl2br(e($agenda->mengingat)) !!}
                    @else
                        <ol class="list-poin">
                            <li>Undang-Undang Nomor 16 Tahun 1997 tentang Statistik;</li>
                            <li>Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;</li>
                        </ol>
                    @endif
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin: 15px 0;" class="bold">Memberi Tugas / Perintah:</div>

        <table class="table-main">
            <tr>
                <td class="col-label">Kepada</td>
                <td class="col-sep">:</td>
                <td class="col-isi bold">(Daftar Nama Terlampir)</td>
            </tr>
            <tr>
                <td class="col-label">Untuk</td>
                <td class="col-sep">:</td>
                <td class="col-isi">Melaksanakan tugas <strong>{{ $agenda->title }}</strong> di Kabupaten Tuban pada tanggal <strong>{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y') }}</strong>.</td>
            </tr>
        </table>

        <div class="footer">
            <div class="ttd-wrapper">
                Tuban, {{ now()->translatedFormat('d F Y') }}<br>
                Kepala BPS Kabupaten Tuban,<br>
                <div style="height: 85px;">
                    @if($agenda->status_approval === 'Approved' && $agenda->approver->signature)
                        <img src="{{ public_path('storage/' . $agenda->approver->signature) }}" class="ttd-image">
                    @endif
                </div>
                <span class="bold" style="text-decoration: underline;">{{ $agenda->approver->nama_lengkap }}</span><br>
                NIP. {{ $agenda->approver->nip }}
            </div>
        </div>

        <div class="page-break"></div>
        <div style="margin-left: 55%; font-size: 9pt;">
            Lampiran Surat Tugas Kepala BPS Kabupaten Tuban<br>
            Nomor : {{ $agenda->nomor_surat_tugas }}<br>
            Tanggal : {{ now()->translatedFormat('d F Y') }}
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <span class="bold uppercase">DAFTAR PEGAWAI YANG DITUGASKAN PADA:</span><br>
            <span class="bold uppercase">{{ $agenda->title }}</span>
        </div>

        <table border="1" width="100%" style="border-collapse: collapse; font-size: 10pt;">
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px;" width="5%">No</th>
                <th style="padding: 8px;">Nama / NIP</th>
                <th style="padding: 8px;">Jabatan</th>
            </tr>
            @foreach($grupPetugas as $index => $item)
            <tr>
                <td align="center" style="padding: 8px;">{{ $index + 1 }}</td>
                <td style="padding: 8px;"><strong>{{ $item->assignee->nama_lengkap }}</strong><br>NIP. {{ $item->assignee->nip ?? '-' }}</td>
                <td style="padding: 8px;">{{ $item->assignee->role }}</td>
            </tr>
            @endforeach
        </table>
    @endif

{{-- ========================================================== --}}
{{-- MODE MEMORANDUM: DINAS RAPAT (2) & DINAS LUAR (3) --}}
{{-- ========================================================== --}}
@elseif($agenda->activity_type_id == 2 || $agenda->activity_type_id == 3)
    <div class="header">
        <img src="{{ public_path('img/logo-bps.png') }}" class="logo-bps">
        <div class="kop-text">
            <span class="kop-bps">BADAN PUSAT STATISTIK</span><br>
            <span class="kop-kab">KABUPATEN TUBAN</span>
        </div>
    </div>

    <div class="title-section">
        <span class="title-spt" style="text-decoration: none;">MEMORANDUM</span><br>
        <span class="bold">NOMOR : {{ $agenda->nomor_surat_tugas }}</span>
    </div>

    <table class="table-main">
        <tr>
            <td class="col-label">Yth</td>
            <td class="col-sep">:</td>
            <td class="col-isi">{{-- GANTI BARIS INI --}}
                {{ $agenda->yth ?? 'Pegawai BPS Kabupaten Tuban' }}</td>
        </tr>
        <tr>
            <td class="col-label">Hal</td>
            <td class="col-sep">:</td>
            <td class="col-isi bold">{{ $agenda->title }}</td>
        </tr>
    </table>

    <p style="margin-top: 15px;">Dengan hormat,</p>
    <p>Dalam rangka {{ $agenda->title }} BPS Kabupaten Tuban, bersama ini mengharap kehadiran/partisipasi Saudara pada:</p>

    <table class="table-memo">
        <thead>
            <tr>
                <th width="30%">Hari/Tanggal</th>
                <th width="20%">Waktu</th>
                <th width="25%">Tempat</th>
                <th width="25%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l') }}<br>
                    {{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d-m-Y') }}
                    @if($agenda->end_date && $agenda->end_date != $agenda->event_date)
                        s.d {{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d-m-Y') }}
                    @endif
                </td>
                <td>{{ $agenda->start_time ? \Carbon\Carbon::parse($agenda->start_time)->format('H.i') : '07.30' }} s.d Selesai</td>
                <td>{{ $agenda->location ?? 'BPS Kabupaten Tuban / Sesuai Undangan' }}</td>
                <td>{!! nl2br(e($agenda->content_surat)) !!}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 15px;">Demikian atas perhatiannya diucapkan terima kasih.</p>

    <div class="footer">
        <div class="ttd-wrapper">
            Tuban, {{ \Carbon\Carbon::parse($agenda->created_at)->translatedFormat('d F Y') }}<br>
            Kepala Badan Pusat Statistik<br>
            Kabupaten Tuban,<br>
            <div style="height: 85px;">
                @if($agenda->status_approval === 'Approved' && $agenda->approver->signature)
                    <img src="{{ public_path('storage/' . $agenda->approver->signature) }}" class="ttd-image">
                @endif
            </div>
            <span class="bold" style="text-decoration: underline;">{{ $agenda->approver->nama_lengkap }}</span><br>
            NIP. {{ $agenda->approver->nip }}
        </div>
    </div>
@endif

</body>
</html>