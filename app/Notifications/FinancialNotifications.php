<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class FinancialNotifications extends Notification
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $htmlLine = 'Please click <a href="'.route('profile/finance').'">here</a> to configure your finance account.';
        return (new MailMessage)
                    ->subject('You forgot to configure your financial account')
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line('Our system indicates that you forgot to configure your financial account. Configuring financial account(s)
                     in our system will help you to process payments automatically. This feature will save you time and help you to stay organized. ')
                    ->line(new HtmlString($htmlLine));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
