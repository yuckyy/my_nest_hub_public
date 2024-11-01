<?php

namespace App\Notifications;

use App\Models\UserIdentity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Support\HtmlString;

class DwollaCustomerVerificationSuccess extends Notification
{
    use Queueable;

    private $user;
    private $identity;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, UserIdentity $identity)
    {
        $this->user = $user;
        $this->identity = $identity;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $htmlLine = '<a href="'.route('profile/identity').'">Go to my account</a>.';
        return (new MailMessage)
            ->subject('Your Identity has been successfully verified')
            ->greeting('Dear ' . ucwords($notifiable->name) . ',')
            ->line('Your Identity has been successfully verified. ')
            ->line(new HtmlString($htmlLine));
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
            'type' => 'VERIFICATION_SUCCESS',
            'url' => route('profile/identity'),
            'creator_user_id' => $this->user->id,
            'user_id' => $this->user->id,
            'description' => 'You are able to receive ACH payments',
            'title' => 'Verification Successful',
        ];
    }
}
