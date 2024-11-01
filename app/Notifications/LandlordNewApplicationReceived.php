<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LandlordNewApplicationReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $sharedUser;
    private $landlord;
    private $application;

    public function __construct(User $sharedUser, User $landlord, Application $application)
    {
        //
        $this->sharedUser = $sharedUser;
        $this->landlord = $landlord;
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = new MailMessage;
        return $mail
            ->subject("MYNESTHUB.com")
            ->markdown('smtp.notifications.landlord_application_received', [
                'sharedUser' => $this->sharedUser,
                'landlord' => $this->landlord,
                'application' => $this->application
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
            'type' => 'APPLICATION_CREATED',
            'url' => route('applications/view', ['id' => $this->application->id]),
            'creator_user_id' => $this->sharedUser->id,
            'user_id' => $notifiable->id,
            'description' => 'from ' . $this->sharedUser->full_name,
            'title' => "New Application",
        ];
    }
}
