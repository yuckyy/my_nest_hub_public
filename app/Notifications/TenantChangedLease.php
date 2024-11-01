<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantChangedLease extends Notification
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
                    ->subject('Your lease has been changed')
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line('There were some changes made to your lease by your landlord. Please review your lease for accuracy and contact your landlord with any issues.');
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
            'type' => 'LEASE_CHANGED',
            'url' => route('tenant/leases'),
            'creator_user_id' => $this->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for '.$this->lease->unit->property->full_address.'.',
            'title' => "Lease Updated",
        ];
    }
}
