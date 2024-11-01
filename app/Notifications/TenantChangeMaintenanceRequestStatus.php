<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;

class TenantChangeMaintenanceRequestStatus extends Notification
{
    use Queueable;

    private $landlord;
    private $tenant;
    private $title;
    private $description;
    private $url;
    private $unit;
    private $property;

    private $tenantPhone;

    private $status;
    private $priority_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $landlord, MaintenanceRequest $maintenanceRequest)
    {
        $this->landlord = $landlord;
        $this->tenant = Auth::user();
        $this->title = $maintenanceRequest->name;
        $this->description = $maintenanceRequest->description;
        $this->url = route('maintenance');

        $this->unit = $maintenanceRequest->unit;
        $this->property = $this->unit->property;

        // TODO !!
        // get tenant's phone from lease (for now we don't have a phone field there)
        //$unit = $maintenanceRequest->unit;
        //if(count($unit->leases) > 0) {
        //    foreach ($unit->leases as $lease) {
        //        if (empty($lease->deleted_at) && ($this->tenant->email == $lease->email)) {
        //            $this->tenantPhone = $lease->phone;
        //            break;
        //        }
        //    }
        //}
        if(empty($this->tenantPhone)){
            $this->tenantPhone = $this->tenant->phone;
        }

        $this->priority_id = $maintenanceRequest->priority_id;
        $this->status = $maintenanceRequest->status;
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
            ->subject("Status of maintenance request has been changed by " . $this->tenant->fullName())
            ->markdown('smtp.notifications.tenant_change_maintenance_request', [
                'landlord' => $this->landlord,
                'tenant' => $this->tenant,

                'title' => $this->title,
                'description' => $this->description,
                'unit' => $this->unit,
                'property' => $this->property,
                'url' => $this->url,

                'tenantPhone' => $this->tenantPhone,

                'status' => $this->status,
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
            'type' => $this->status->id == 3 ? 'MAINTENANCE_RESOLVED' : 'MAINTENANCE',
            'url' => $this->url,
            'creator_user_id' => $this->tenant->id,
            'user_id' => $this->landlord->id,
            'description' => $this->title . " - " . $this->description,
            'title' => "New Status: " . $this->status->name,

            'priority_id' => $this->priority_id,
        ];
    }
}
