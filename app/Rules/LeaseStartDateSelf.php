<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Unit;
use App\Models\Lease;
use Illuminate\Support\Facades\DB;

class LeaseStartDateSelf implements Rule
{
    protected $unit_id;
    protected $start_date;
    protected $end_date;
    protected $message;
    protected $fullMessage;
    protected $lease_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($unit_id, $end_date, $lease_id)
    {
        $this->unit_id = $unit_id;
        $this->end_date = $end_date;
        $this->lease_id = $lease_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->start_date = $value;
        $success = true;

        if(!empty($this->end_date) && ($this->start_date > $this->end_date)){
            $this->fullMessage = "Start Date shouldn't be greater than End Date";
            $success = false;
            return $success;
        }

        //check if we may change start date
        $query1 = DB::table('payments');
        $query1->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id');
        //invoice based on lease
        $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
        $query1->where('is_lease_pay', '=', '1');
        $query1->where('leases.id', '=', $this->lease_id);
        $query1->where('payment_method', '!=', 'Generated');
        $leasePaymentsCount = $query1->count();

        $query = DB::table('invoices');
        // invoice based on bill
        $query->join('bills', 'bills.id', '=', 'invoices.base_id');
        $query->join('leases', 'leases.id', '=', 'bills.lease_id');
        $query->where('is_lease_pay', '=', '0');
        $query->where('leases.id', '=', $this->lease_id);
        $billsCount = $query->count();

        if(($leasePaymentsCount > 0) || ($billsCount > 0)){
            $this->fullMessage = "You can't change Lease Start date because there was payment(s) made or there was an additional bill(s) created.";
            $success = false;
            return $success;
        }

        $conflicted_leases =
            Lease::where('unit_id', $this->unit_id)->where('id', '!=', $this->lease_id)->where('start_date', '<=', $value)->where('deleted_at', '>=', $value)->whereNull('end_date')
                ->orWhere('unit_id', $this->unit_id)->where('id', '!=', $this->lease_id)->where('start_date', '<=', $value)->where('end_date', '>=', $value)
                //->withTrashed()
                ->get();
        $this->message = "";
        foreach($conflicted_leases as $lease) {
            if (!$lease->end_date || ($lease->deleted_at < $lease->end_date)) {
                $end_date = $lease->deleted_at;
            } else {
                $end_date = $lease->end_date;
            }
            $this->message .= $lease->firstname . " " . $lease->firstname . ", lease dates: " . date("M j, Y",strtotime($lease->start_date)) . " - " . ( $end_date ? date("M j, Y",strtotime($end_date)) : " (no end date)") . ". ";
            $success = false;
        }

        if($this->end_date) {

            $conflicted_leases =
                Lease::where('unit_id', $this->unit_id)->where('id', '!=', $this->lease_id)->where('start_date', '>', $this->start_date)->where('start_date', '<', $this->end_date)
                    ->where('end_date', '>', $this->start_date)->where('end_date', '<', $this->end_date)
                    ->orWhere('unit_id', $this->unit_id)->where('id', '!=', $this->lease_id)->where('start_date', '>', $this->start_date)->where('start_date', '<', $this->end_date)
                    ->where('deleted_at', '>', $this->start_date)->where('deleted_at', '<', $this->end_date)->whereNull('end_date')
                    //->withTrashed()
                    ->get();

            foreach ($conflicted_leases as $lease) {
                if (!$lease->end_date || ($lease->deleted_at < $lease->end_date)) {
                    $end_date = $lease->deleted_at;
                } else {
                    $end_date = $lease->end_date;
                }
                $this->message .= $lease->firstname . " " . $lease->firstname . ", lease dates: " . date("M j, Y", strtotime($lease->start_date)) . " - " . ($end_date ? date("M j, Y", strtotime($end_date)) : " (no end date)") . ". ";
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if(!empty($this->fullMessage)){
            return $this->fullMessage;
        } else {
            return 'Entered Lease Start date conflicts with another lease: ' . $this->message . 'Enter different dates for Lease.';
        }
    }
}
