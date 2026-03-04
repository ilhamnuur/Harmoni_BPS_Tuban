<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function collection() {
        return $this->data;
    }

    public function headings(): array {
        return [
            'Nama Pegawai', 'Tujuan Pengawasan', 'Lokasi', 'No Surat Tugas', 'Tanggal', 'Status'
        ];
    }

    public function map($agenda): array {
        return [
            $agenda->assignee->nama_lengkap,
            $agenda->title,
            $agenda->location,
            $agenda->nomor_surat_tugas,
            \Carbon\Carbon::parse($agenda->event_date)->format('d/m/Y'),
            $agenda->status_laporan,
        ];
    }
}