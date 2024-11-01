<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ListOfTenantsWhoDidntPay extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $tenants_info;
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
        if(!empty($notifiable->preferences) && $notifiable->preferences->notify_if_tenants_not_pay) {
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
            ->subject("You didn’t receive some monthly payments for rent")
            ->markdown('smtp.notifications.list_of_tenants_who_didnt_pay', [
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
