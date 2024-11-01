<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use App\Models\SubscriptionPlan;

class SubscriptionReceipt extends Notification
{
    use Queueable;
    use Notifiable;

    protected $plan;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SubscriptionPlan $plan, $type)
    {
        $this->plan = $plan;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->type == "buying") {
            return (new MailMessage)
                        ->subject('Thank you for buying membership at '. config('app.name'))
                        ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                        ->line(new HtmlString('Thank you for purchasing '.$this->plan->name.' membership with '.config('app.name').'. <br>Below is your receipt: <br>'.$this->plan->name.' plan: $'.$this->plan->price.'.'));
        }
        return (new MailMessage)
                    ->subject('Thank you for updating membership at '. config('app.name'))
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line(new HtmlString('Thank you for updating membership with '.config('app.name').'. <br>Below is your receipt: <br>'.$this->plan->name.' plan: $'.$this->plan->price.'.'));
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
            //
        ];
    }
}
