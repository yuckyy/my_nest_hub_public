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

class Register extends VerifyEmail
{
    use Queueable;
    use Notifiable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $name = $notifiable->name;
        $MYNESTHUB = url('https://MYNESTHUB.com');
        $url = route('easy-email-verify', ['id' => $notifiable->getKey()]);
        /*$url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );*/

        return (new MailMessage)
            ->subject('Your account on MYNESTHUB has been created')
            ->greeting(Lang::getFromJson(':name', ['name' => $name]))
            //->line(Lang::getFromJson('Welcome to MYNESTHUB!'))
            //->line(Lang::getFromJson('We received a request to reset the password associated with this e-mail address. If you made this request, please follow the instructions below.Click the link below to reset your password using our secure server:'))
            ->markdown('smtp.notifications.email', [
                //'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                'url' => $MYNESTHUB,
                'name' => $name,
                'verifyurl' => $url,
            ]);
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
