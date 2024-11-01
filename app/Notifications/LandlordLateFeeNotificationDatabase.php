<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class LandlordLateFeeNotificationDatabase extends Notification
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
        //$tenant = User::where('email',$this->lease->email)->first();
        if(empty($tenant)){
            $tenantId = null;
        } else {
            $tenantId = $tenant->id;
        }
        return [
            'type' => 'PAYMENT_OUTSTANDING',
            'url' => route('properties/units/leases', ['unit' => $this->lease->unit->id ]),
            'creator_user_id' => $tenantId,
            'user_id' => $notifiable->id,
            'description' => 'for ' . $this->lease->unit->property->full_address . '.',
            'title' => "Tenant didn’t send a payments",
        ];
    }
}
