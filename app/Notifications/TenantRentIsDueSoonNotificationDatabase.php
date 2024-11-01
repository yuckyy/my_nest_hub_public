<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class TenantRentIsDueSoonNotificationDatabase extends Notification
{
//    use Queueable;

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
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'type' => 'PAYMENT_SOON',
            'url' => route('tenant/leases'),
            'creator_user_id' => $this->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for ' . $this->lease->unit->property->full_address . '.',
            'title' => "Rent payment is coming soon",
        ];
    }
}
