<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Complaint;

class ComplaintStatusChanged extends Notification
{
    use Queueable;
    protected $complaint;

    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Update Status Pengaduan - ' . $this->complaint->registration_number;
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Status pengaduan Anda telah diupdate.')
            ->line('**Nomor Registrasi:** ' . $this->complaint->registration_number)
            ->line('**Layanan:** ' . $this->complaint->service->name)
            ->line('**Status:** ' . $this->complaint->getStatusLabel())
            ->line('**Tanggal Update:** ' . $this->complaint->updated_at->format('d/m/Y H:i'));

        if ($this->complaint->status === 'needs_revision') {
            $message->line('**Catatan:** Pengaduan Anda memerlukan revisi. Silakan periksa detail pengaduan untuk informasi lebih lanjut.');
            $message->action('Lihat Detail Pengaduan', route('complaints.show', $this->complaint));
        } elseif ($this->complaint->status === 'completed') {
            $message->line('Selamat! Pengaduan Anda telah selesai diproses.');
            $message->action('Download Dokumen Hasil', route('complaints.show', $this->complaint));
        } else {
            $message->action('Cek Status Pengaduan', route('complaints.show', $this->complaint));
        }

        $message->line('Terima kasih telah menggunakan layanan kami.')
                ->salutation('Salam, Tim Pelayanan Publik Kabupaten Badung');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'registration_number' => $this->complaint->registration_number,
            'status' => $this->complaint->status,
            'service_name' => $this->complaint->service->name,
        ];
    }
}
