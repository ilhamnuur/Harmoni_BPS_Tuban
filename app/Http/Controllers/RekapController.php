<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class RekapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */

    public function exportRekapPDF()
    {
        $riwayat = $this->getFilteredHistoryData();
        $pdf = Pdf::loadView('history.pdf_rekap', compact('riwayat'))->setPaper('f4', 'portrait');
        return $pdf->download('Rekap_Laporan_BPS_'.date('dmy').'.pdf');
    }

    public function exportRekapExcel()
    {
        $riwayat = $this->getFilteredHistoryData();
        $fileName = 'Rekap_Laporan_BPS_'.date('dmy_His').'.xls';
        $html = view('history.excel_rekap', compact('riwayat'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPER
    |--------------------------------------------------------------------------
    */

    private function generateFileName(string $extension): string
    {
        $timestamp = Carbon::now()->format('dmy_His');
        return "Rekap_Laporan_BPS_{$timestamp}.{$extension}";
    }
}