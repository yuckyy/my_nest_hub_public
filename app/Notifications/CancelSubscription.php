<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class CancelSubscription extends Notification
{
    use Queueable;
    use Notifiable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {

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
        if ($notifiable->isTenant()) {
            return (new MailMessage)
                ->subject('Your landlord is no longer using our system')
                ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                ->line('Your landlord is not using our system any more and because of that your lease is no longer active on MYNESTHUB. If you have any questions, please contact your landlord.');
        }
        return (new MailMessage)
            ->subject('Your membership had been cancelled.')
            ->greeting('Dear ' . ucwords($notifiable->name) . ',')
            ->line('Your membership with MYNESTHUB had been cancelled. We are sorry to let you go but if you decide to change your mind, you can always to register at our site again.');
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
