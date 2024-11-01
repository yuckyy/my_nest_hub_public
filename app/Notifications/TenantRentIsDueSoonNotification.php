<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantRentIsDueSoonNotification extends Notification
{
//    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $r = [];
        if(!empty($notifiable->preferences) && $notifiable->preferences->notify_if_rent_is_due_soon) {
            $r[] = 'mail';
        }
        return $r;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //print $notifiable->email. PHP_EOL;
        return (new MailMessage)
            ->subject('Your rent payment is coming soon')
            ->markdown('smtp.notifications.rent_is_due_soon', [
                'tenant' => ucwords($notifiable->firstname),
                'loginUrl' => url('/'),
                'user' => $notifiable
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
