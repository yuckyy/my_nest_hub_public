<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class LeaseOfTenantEnded extends Notification
{
    use Queueable;
    use Notifiable;

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
        if(!empty($notifiable->preferences) && $notifiable->preferences->notify_if_leases_ended) {
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
            ->subject("Lease(s) ended")
            ->markdown('smtp.notifications.list_of_leases_is_end_today', [
                'landlord' => ucwords($notifiable->name),
                'tenants_info' => $this->tenants_info,
                'login_url' => url('/'),
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
