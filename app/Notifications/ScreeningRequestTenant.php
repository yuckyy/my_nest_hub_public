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

class ScreeningRequestTenant extends Notification
{
    use Queueable;
    use Notifiable;

    private $firstName, $lastName, $quickappApplicantLink, $email;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($firstName, $lastName, $quickappApplicantLink, $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->quickappApplicantLink = $quickappApplicantLink;
        $this->email = $email;
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
            ->subject('MYNESTHUB.com Background Questionnaire Notification')
            ->markdown('smtp.notifications.screening_request_tenant', [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'quickappApplicantLink' => $this->quickappApplicantLink,
                'email' => $this->email,
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
