<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Unit;
use App\Models\Lease;

class LeaseStartDate implements Rule
{
    protected $unit_id;
    protected $start_date;
    protected $end_date;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($unit_id, $end_date)
    {
        $this->unit_id = $unit_id;
        $this->end_date = $end_date;
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
        $conflicted_leases =
            Lease::where('unit_id', $this->unit_id)->where('start_date', '<=', $value)->where('deleted_at', '>=', $value)->whereNull('end_date')
                ->orWhere('unit_id', $this->unit_id)->where('start_date', '<=', $value)->where('end_date', '>=', $value)
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
                Lease::where('unit_id', $this->unit_id)->where('start_date', '>', $this->start_date)->where('start_date', '<', $this->end_date)
                    ->where('end_date', '>', $this->start_date)->where('end_date', '<', $this->end_date)
                    ->orWhere('unit_id', $this->unit_id)->where('start_date', '>', $this->start_date)->where('start_date', '<', $this->end_date)
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
        return 'Entered Lease Start date conflicts with another lease: ' . $this->message . 'Enter different dates for Lease.';
    }
}
