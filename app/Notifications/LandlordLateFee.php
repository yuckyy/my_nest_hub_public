<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LandlordLateFee extends Notification
{
    use Queueable;

    private $tenants_info;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($tenants_info)
    {
        $this->tenants_info = $tenants_info;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $r = [];
        if(!empty($notifiable->preferences) && $notifiable->preferences->notify_if_late_fees_applied) {
            $r[] = 'mail';
        }
        return $r;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //print $notifiable->email. PHP_EOL;
        return (new MailMessage)
            ->subject("Late fees had been applied to tenant(s)")
            ->markdown('smtp.notifications.late_fee_landlord', [
                'landlord' => ucwords($notifiable->name),
                'tenants_info' => $this->tenants_info,
                'user' => $notifiable
            ]);
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
