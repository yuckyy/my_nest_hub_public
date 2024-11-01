<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantSharedApplication extends Notification
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
    private $toEmail;
    private $toUser;

    public function __construct(User $sharedUser, Application $application, string $toEmail)
    {
        //
        $this->sharedUser = $sharedUser;
        $this->application = $application;
        $this->toEmail = $toEmail;
        $this->toUser = User::where('email',$this->toEmail)->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if(!empty($this->toUser)){
            return ['mail', 'database'];
        } else {
            return ['mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $sharedUser = $this->sharedUser;
        $application = $this->application;

        return (new MailMessage)
                    ->subject($sharedUser->roles->first()->name ." $sharedUser->name $sharedUser->lastname wants to share tenant application with you!")
                    ->markdown('smtp.notifications.shared_application', [
                        'sharedUser' => $sharedUser,
                        'application' => $application,
                        'landlordEmail' => $this->toEmail
                    ]);
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
        if(!empty($this->toUser)){
            return [
                'type' => 'APPLICATION_CREATED',
                'url' => route('applications/view', ['id' => $this->application->id]),
                'creator_user_id' => $this->sharedUser->id,
                'user_id' => $this->toUser->id,
                'description' => 'created for you by '.$this->sharedUser->full_name,
                'title' => "New Application",
            ];
        } else {
            return [];
        }
    }
}
