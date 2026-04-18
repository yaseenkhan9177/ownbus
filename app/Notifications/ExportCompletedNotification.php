<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $fileUrl;
    public $fileName;

    /**
     * Create a new notification instance.
     */
    public function __construct($fileUrl, $fileName)
    {
        $this->fileUrl = $fileUrl;
        $this->fileName = $fileName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'export_completed',
            'title' => 'Export Completed Successfully',
            'message' => "Your file {$this->fileName} is ready for download.",
            'url' => $this->fileUrl,
            'icon' => 'document-download',
            'is_read' => false,
        ];
    }
}
