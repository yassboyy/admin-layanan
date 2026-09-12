<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountAutoCreatedNotification extends Notification
{
    use Queueable;

    public string $token;
    public string $serviceName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $serviceName = '')
    {
        $this->token = $token;
        $this->serviceName = $serviceName;
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
        $email = method_exists($notifiable, 'getEmailForPasswordReset')
            ? $notifiable->getEmailForPasswordReset()
            : ($notifiable->email ?? '');

        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ]);

        return (new MailMessage)
            ->subject('Akun Anda Telah Dibuat di SIADO - CV Tomo Teknik Mandiri')
            ->view('emails.account-auto-created', [
                'name' => $notifiable->name ?? 'Pelanggan',
                'email' => $email,
                'resetUrl' => $resetUrl,
                'serviceName' => $this->serviceName,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'service_name' => $this->serviceName,
        ];
    }
}
