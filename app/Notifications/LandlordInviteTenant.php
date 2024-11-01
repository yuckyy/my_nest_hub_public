<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LandlordInviteTenant extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $landlord;
    private $unit;
    private $toEmail;

    public function __construct(User $landlord, $unit, string $toEmail)
    {
        $this->landlord = $landlord;
        $this->unit = $unit ?? null;
        $this->toEmail = $toEmail;
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
                    ->subject("Landlord " . $this->landlord->name . " " . $this->landlord->lastname ." would like you to submit rental application")
                    ->markdown('smtp.notifications.landlord_invite_tenant', [
                        'landlord' => $this->landlord,
                        'unit' => $this->unit ?? null,
                        'toEmail' => $this->toEmail
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //
    }
}
