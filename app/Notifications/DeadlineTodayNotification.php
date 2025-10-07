<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineTodayNotification extends Notification implements ShouldQueue {
    use Queueable;

    protected $projects;
    protected $tasks;

    /**
     * Create a new notification instance.
     */
    public function __construct($projects, $tasks) {
        $this->projects = $projects;
        $this->tasks = $tasks;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Reminder: Deadline Hari ini')
            ->greeting('Hai, ' . $notifiable->name . '!')
            ->line('Beberapa project dan task kamu akan mencapai deadline hari ini.')
            ->line('Jumlah Project: ' . $this->projects->count())
            ->line('Jumlah Task: ' . $this->tasks->count())

            // ->action('Lihat sekarang ' . url('/dashboard'))
            ->line('Lihat sekarang [nanti kasih link]')

            ->line('Segera selesaikan sebelum waktu habis!.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array {
        return [
            //
        ];
    }

    public function toDatabase(object $notifiable) {
        return [
            'projects' => $this->projects->pluck('name'),
            'tasks' => $this->tasks->pluck('title'),
            'message' => 'Kamu memiliki deadline hari ini.'
        ];
    }
}
