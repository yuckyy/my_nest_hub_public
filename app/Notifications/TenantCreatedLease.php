<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class TenantCreatedLease extends Notification
{
    use Queueable;

    private $lease;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($lease)
    {
        $this->lease = $lease;
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
        $htmlLine = 'Please <a href="'.route('tenant/leases').'">click here</a> to view your lease.';

        return (new MailMessage)
                    ->subject('Your Lease from ' . ucwords($this->lease->unit->property->user->full_name))
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line('Your landlord, '.ucwords($this->lease->unit->property->user->full_name).' created a lease for you for property located at '.$this->lease->unit->property->full_address.'.')
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
            'type' => 'LEASE_CREATED',
            'url' => route('tenant/leases'),
            'creator_user_id' => $this->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for you for property located at '.$this->lease->unit->property->full_address.'.',
            'title' => "New Lease",
        ];
    }
}
