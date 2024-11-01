<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use App\Models\Addon;

class AddonReceipt extends Notification
{
    use Queueable;
    use Notifiable;

    protected $addon;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Addon $addon)
    {
        $this->addon = $addon;
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
        return (new MailMessage)
                    ->subject('Thank you for buying the addon '.$this->addon->title.' with '. config('app.name'))
                    ->greeting('Dear ' . ucwords($notifiable->name) . ',')
                    ->line(new HtmlString('Thank you for purchasing '.$this->addon->title.' addon with '.config('app.name').'. <br>Below is your receipt: <br>'.$this->addon->title.' addon: $'.$this->addon->price.'.'));
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
