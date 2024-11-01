<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;

class LandlordCreateMaintenanceRequest extends Notification
{
    use Queueable;

    private $landlord;
    private $tenant;
    private $title;
    private $description;
    private $url;
    private $unit;
    private $property;

    private $priority_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $tenant, MaintenanceRequest $maintenanceRequest)
    {
        $this->landlord = Auth::user();
        $this->tenant = $tenant;
        $this->title = $maintenanceRequest->name;
        $this->description = $maintenanceRequest->description;
        $this->url = route('maintenance');

        $this->unit = $maintenanceRequest->unit;
        $this->property = $this->unit->property;

        $this->priority_id = $maintenanceRequest->priority_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if(empty($this->tenant)){
            return [];
        }
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
            ->subject("New Maintenance Request has been created from " . $this->landlord->fullName())
            ->markdown('smtp.notifications.landlord_create_maintenance_request', [
                'landlord' => $this->landlord,
                'tenant' => $this->tenant,

                'title' => $this->title,
                'description' => $this->description,
                'unit' => $this->unit,
                'property' => $this->property,
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
            'type' => 'MAINTENANCE',
            'url' => $this->url,
            'creator_user_id' => $this->landlord->id,
            'user_id' => $this->tenant->id,
            'description' => $this->description,
            'title' => $this->title,

            'priority_id' => $this->priority_id,
        ];
    }
}
