<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Unit;
use App\Models\Lease;

class MonthToMonth implements Rule
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
    public function __construct($unit_id, $start_date)
    {
        $this->unit_id = $unit_id;
        $this->start_date = $start_date;
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
        if(!$value){
            return true;
        }
        $success = true;
        $conflicted_leases =
            Lease::where('unit_id', $this->unit_id)->where('start_date', '>', $this->start_date)
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

        return $success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Entered Lease End date conflicts with another lease: ' . $this->message . 'Enter different dates for Lease';
    }
}
