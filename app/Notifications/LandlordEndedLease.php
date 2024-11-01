<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LandlordEndedLease extends Notification
{
    use Queueable;

    private $lease;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($lease)
    {
        $this->lease = $lease;
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
        return (new MailMessage)
                    ->subject('Lease has ended for '.$this->lease->unit->property->full_address.' '.$this->lease->unit->name)
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line('The lease for '.$this->lease->unit->property->full_address.' '.$this->lease->unit->name.' has now ended. This unit is now vacant for new tenants.');
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
