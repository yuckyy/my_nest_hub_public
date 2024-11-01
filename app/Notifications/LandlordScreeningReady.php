<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\AddonScreening;

class LandlordScreeningReady extends Notification
{
    use Queueable;

    private $landlord;
    private $tenant;
    private $title;
    private $description;
    private $screening;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $landlord, AddonScreening $screening)
    {
        $this->screening = $screening;
        $this->landlord = $landlord;
        $this->title = 'Report is ready';
        $this->description = 'Tenant screening report for ' . $screening->firstName . " " . $screening->lastName . ' is ready.';
        $this->url = route('applications/view',['id' => $screening->application_id ]);
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
        $mail = new MailMessage;
        return $mail
            ->subject($this->description)
            ->markdown('smtp.notifications.landlord_screening_ready', [
                'landlord' => $this->landlord,
                'description' => $this->description,
                'url' => $this->url,
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
            'type' => 'SCREENING',
            'url' => $this->url,
            'creator_user_id' => $this->landlord->id,
            'user_id' => $this->landlord->id,
            'description' => $this->description,
            'title' => $this->title,
        ];
    }
}
