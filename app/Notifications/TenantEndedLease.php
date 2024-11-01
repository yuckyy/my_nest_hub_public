<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantEndedLease extends Notification
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
        return ['mail', 'database'];
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
                    ->line('The lease for '.$this->lease->unit->property->full_address.' '.$this->lease->unit->name.' has now ended.  You still can access your portal and view all of your financial reports. You can no longer send payments to your landlord, and all of your payments will no longer recur.');
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
            'type' => 'LEASE_ENDED',
            'url' => route('tenant/leases'),
            'creator_user_id' => $this->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for '.$this->lease->unit->property->full_address.' '.$this->lease->unit->name,
            'title' => "Lease Ended",
        ];
    }
}
