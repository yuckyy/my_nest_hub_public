<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShareUnit extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $landlord;
    private $tenant;
    private $text;
    private $property;
    private $unit;

    public function __construct(User $landlord, Property $property, Unit $unit, $tenant, string $text = '')
    {
        //
        $this->landlord = $landlord;
        $this->text = $text;
        $this->tenant = $tenant;
        $this->property = $property;
        $this->unit = $unit;
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
        $landlord = $this->landlord;
        $unit = $this->unit;
        $property = $this->property;

        return (new MailMessage)
                    ->subject("$landlord->name $landlord->lastname shared information about $property->full_address $unit->name")
                    ->markdown('smtp.notifications.share_unit', [
                        'landlord' => $landlord,
                        'unit' => $unit,
                        'tenant' => $this->tenant,
                        'property' => $this->property,
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
            //
        ];
    }
}
