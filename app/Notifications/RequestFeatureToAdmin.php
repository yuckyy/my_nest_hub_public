<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notifiable;

class RequestFeatureToAdmin extends Notification
{
    use Queueable;
    use Notifiable;

    private $feature;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($feature, $user)
    {
        $this->feature = $feature;
        $this->user = $user;
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
            ->subject('MYNESTHUB.com New Feature Request')
            ->greeting('Dear Administarator,')
            ->line('New Feature Request on MYNESTHUB:')
            ->line($this->feature)
            ->line('Requested by:')
            ->line($this->user->name . ' ' . $this->user->lastname . ', ' . $this->user->email);
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
