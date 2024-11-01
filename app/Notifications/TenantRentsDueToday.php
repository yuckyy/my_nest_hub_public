<?php

namespace App\Notifications;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantRentsDueToday extends Notification
{
    //TODO Check if this class really needed. It looks like we don't use this

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $property;
    private $unit;
    public function __construct(Property $property, Unit $unit)
    {
        $this->property = $property;
        $this->unit = $unit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You missed your payment')
            ->markdown('smtp.notifications.rents_due_today', [
                'unit' => $this->unit,
                'tenant' => ucwords($notifiable->name),
                'property' => $this->property,
            ])
            ;
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
