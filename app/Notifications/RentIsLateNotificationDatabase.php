<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RentIsLateNotificationDatabase extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
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
            'type' => 'PAYMENT_OUTSTANDING',
            'url' => route('tenant/leases'),
            'creator_user_id' => $this->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for ' . $this->lease->unit->property->full_address . '.',
            'title' => "You missed your payment",
        ];
    }
}
