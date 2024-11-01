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

class CalendarEvent extends Notification
{
    use Queueable;
    use Notifiable;

    private $user, $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $event)
    {
        $this->user = $user;
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $r = ['database'];
        if (!empty($notifiable->preferences) && $notifiable->preferences->notify_if_calendar_event) {
            $r[] = 'mail';
        }
        return $r;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //print 'Calendar: '.$notifiable->email. PHP_EOL;
        return (new MailMessage)
            ->subject($this->event->title . 'Your MYNESTHUB.com Calendar Event Today')
            ->markdown('smtp.notifications.calendar_event', [
                'firstName' => $this->user->name,
                'lastName' => $this->user->lastname,
                'link' => route('fullcalendar'),
                'event' => $this->event,
                'user' => $this->user
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
            'type' => 'CALENDAR_EVENT',
            'url' => route('fullcalendar'),
            'creator_user_id' => $this->user->id,
            'user_id' => $notifiable->id,
            'description' => $this->event->title,
            'title' => "Calendar Event",
        ];
    }
}
