<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public static function landlordEmailPreferencesList()
    {
        return [
            [
                'field' => 'notify_if_leases_about_to_end',
                'title' => 'Lease(s) are about to end',
                'description' => 'We will send an email with the list of leases that are about to end.',
                //+Notification handler: $user->notify(new LeaseIsAboutToEnd($tenantToEndInfo))
            ],
            [
                'field' => 'notify_if_leases_ended',
                'title' => 'Lease(s) ended',
                'description' => 'We will send an email with the list of leases that had been ended.',
                //+Notification handler: $user->notify(new LeaseOfTenantEnded($tenantEndedInfo))
            ],
            [
                'field' => 'notify_if_tenants_not_pay',
                'title' => 'You didn’t receive some monthly payments for rent',
                'description' => 'We will send an email with the list of tenants who did not pay the rent on due date.',
                //+Notification handler: $user->notify(new ListOfTenantsWhoDidntPay($lateInfo))
            ],
            [
                'field' => 'notify_if_late_fees_applied',
                'title' => 'Late fees had been applied to the tenant(s)',
                'description' => 'We will send an email with the list of tenants who had been received an automatic late fee for unpaid rent.',
                //+Notification handler: $user->notify(new LandlordLateFee($lateInfo))
            ],
            [
                'field' => 'notify_if_calendar_event',
                'title' => 'Send Calendar of Event Reminder',
                'description' => 'We will send an email about your upcoming calendar of events.',
                //Notification handler: $event->user->notify(new CalendarEvent($event->user, $event))
            ],
        ];
    }

    public static function tenantEmailPreferencesList()
    {
        return [
            [
                'field' => 'notify_if_rent_is_due_soon',
                'title' => 'Your rent payment is coming soon',
                'description' => 'We will send an email when an invoice for the new rent payment has been generated.',
                //Notification handler: $tenant->notify(new TenantRentIsDueSoonNotification())
            ],
        ];
    }
}
