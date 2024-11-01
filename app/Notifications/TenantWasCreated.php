<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;

class TenantWasCreated extends Notification
{
    use Queueable;
    use Notifiable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $tenant;
    private $landlord;

    public function __construct(User $tenant, User $landlord = null)
    {
        //
        $this->tenant = $tenant;
        $this->landlord = $landlord;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $tenant = $this->tenant;
        $landlord = $this->landlord;

        $mail = new MailMessage;

        return $mail
            ->subject("MYNESTHUB.com")
            ->markdown('smtp.notifications.finish_registration', [
                'tenant' => $tenant,
                'url' => route('registration/finish', ['email' => $tenant->email]),
                'landlord' => $landlord,

            ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
