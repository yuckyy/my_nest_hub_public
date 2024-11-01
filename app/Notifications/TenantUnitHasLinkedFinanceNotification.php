<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantUnitHasLinkedFinanceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $landlord;

    public function __construct($landlord)
    {
        $this->landlord = $landlord;
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
                    ->subject("Landlord accepts payments online")
                    ->markdown('smtp.notifications.unit_has_linked_finance', [
                        'landlord' => $this->landlord->full_name,
                        'tenant' => ucwords($notifiable->full_name),
                        'actionUrl' => route('payments'),
                    ])
            ;

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
            'type' => 'LINKED_FINANCE',
            'url' => route('payments'),
            'creator_user_id' => $this->landlord->id,
            'user_id' => $notifiable->id,
            'description' => 'so you can begin paying your rent online.',
            'title' => "Your landlord has enabled online rent payments",
        ];
    }
}
