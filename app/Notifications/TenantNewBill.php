<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class TenantNewBill extends Notification
{
    use Queueable;

    private $bill;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($bill)
    {
        $this->bill = $bill;
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
        $due_date = $this->bill->due_date ? 'due on '. \Carbon\Carbon::parse($this->bill->due_date)->format("m/d") : '';
        return (new MailMessage)
                    ->subject('New Bill(s) have been posted to your account')
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line('New bill(s) have been posted to your account:')
                    ->line(ucfirst(strtolower($this->bill->name)) . ', ' . $due_date . ' $' . $this->bill->value)
                    ->line(new HtmlString('If you have already connected your financial information to the portal, you can make payments directly <a href="'.route('payments',['lease'=>$this->bill->lease_id]).'">here</a>. If you have not yet, click <a href="'.route('profile/finance').'">here</a> to get started.'));
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
            'type' => 'BILL_ADDED',
            'url' => route('payments'),
            'creator_user_id' => $this->bill->lease->unit->property->user->id,
            'user_id' => $notifiable->id,
            'description' => 'for '.$this->bill->lease->unit->property->full_address.'.',
            'title' => "New Bill Added",
        ];
    }
}
