<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPassword extends ResetPasswordNotification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $email = Request()->all()['email'];
        $name = DB::table('users')
            ->select(DB::raw('name'))
            ->where('email', '=', $email)
            ->get();
        $name = $name[0]->name;
        $MYNESTHUB = url('https://MYNESTHUB.com');

        return (new MailMessage)
            ->line(Lang::getFromJson('We received a request to reset the password associated with this e-mail address. If you made this request, please follow the instructions below.'))
            ->line(Lang::getFromJson('Click the link below to reset your password using our secure server:'))
            ->action(Lang::getFromJson('Reset Password'), url(config('app.url') . route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->markdown('smtp.notifications.reset', [
                'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
                'url' => $MYNESTHUB,
                'name' => $name,
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
