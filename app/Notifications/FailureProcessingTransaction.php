<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class FailureProcessingTransaction extends Notification
{
    use Queueable;

    private $lease;
    private $title;
    private $description;
    private $url;
    private $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($lease, $recurring)
    {
        // ONLY for Tenant

        $this->lease = $lease;
        if($recurring){
            $this->title = 'Recurring Payment Error';
            $this->subject = 'Recurring Payment Error';
            $this->description = 'There was an issue with processing your recurring payment for ' . $this->lease->unit->property->address;
        } else {
            $this->title = 'Payment Error';
            $this->subject = 'Payment Error';
            $this->description = 'There was an issue with processing your payment for ' . $this->lease->unit->property->address;
        }
        $this->url = route('payments',['lease' => $lease->id ]);
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
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Dear ' . ucwords($notifiable->name) . ',')
            ->line($this->description)
            ->line(new HtmlString('<a href="'.$this->url.'">View Lease</a>'));
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
            'type' => 'TRANSACTION_ERROR',
            'url' => $this->url,
            'creator_user_id' => $notifiable->id,
            'user_id' => $notifiable->id,
            'description' => 'for ' . $this->lease->unit->property->address,
            'title' => $this->title,
        ];
    }
}
