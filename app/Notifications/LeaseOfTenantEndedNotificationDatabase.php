<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class LeaseOfTenantEndedNotificationDatabase extends Notification
{
    use Queueable;
    use Notifiable;

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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage);
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
            'url' => route('properties/units/leases', ['unit' => $this->lease->unit->id ]),
            'creator_user_id' => null,
            'user_id' => $notifiable->id,
            'description' => 'for ' . $this->lease->unit->property->full_address . ", " . $this->lease->unit->name,
            'title' => "Lease is ended",
        ];
    }
}
