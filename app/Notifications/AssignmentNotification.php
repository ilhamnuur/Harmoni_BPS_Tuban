<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $agenda;

    public function __construct($agenda)
    {
        $this->agenda = $agenda;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $agenda = $this->agenda;
    $type = $agenda->activity_type_id;

    // Format tanggal dasar
    $startDate = \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y');
    $endDate = \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y');

    // LOGIKA TANGGAL: 
    // Jika Tugas Lapangan (1) dan tanggalnya beda, tampilkan rentang. 
    // Jika Rapat (2) atau Dinas Luar (3), cukup tampilkan Tanggal Pelaksanaan.
    if ($type == 1 && $agenda->event_date != $agenda->end_date) {
        $dateInfo = $startDate . ' s.d ' . $endDate;
        $labelTanggal = 'Rentang Waktu';
    } else {
        $dateInfo = $startDate;
        $labelTanggal = 'Tanggal Pelaksanaan';
    }

    return (new MailMessage)
                ->subject('Penugasan Baru: ' . $agenda->title)
                ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
                ->line('Anda telah menerima penugasan baru di aplikasi **HARMONI**.')
                ->line('**Nama Kegiatan:** ' . $agenda->title)
                ->line('**' . $labelTanggal . ':** ' . $dateInfo)
                
                // Menampilkan Tim yang memberikan tugas
                ->line('**Tugas dari:** ' . ($agenda->team->nama_tim ?? 'Umum/Lainnya'))
                
                // Jika ini Rapat (ID 2), boleh tambahkan jam biar makin informatif
                ->line($type == 2 ? '**Waktu:** ' . ($agenda->start_time ?? '08:00') . ' WIB' : '')

                ->action('Lihat Detail Penugasan', url('/task'))
                ->line('Mohon segera laksanakan tugas dan laporkan hasilnya tepat waktu.')
                ->salutation('Terima Kasih, Admin HARMONI');
}
}