<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RentAssistance implements Rule
{
    protected $military;
    protected $section8;
    protected $other;
    protected $lease_amount;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($military, $section8, $other, $lease_amount)
    {
        $this->military = (float) str_replace(",","",$military);
        $this->section8 = (float) str_replace(",","",$section8);
        $this->other = (float) str_replace(",","",$other);
        $this->lease_amount = (float) str_replace(",","",$lease_amount);

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
        if(((float)$value) == 0){
            return true;
        }
        $success = ((float) $this->lease_amount) > ((float) $this->military + (float) $this->section8 + (float) $this->other);
        return $success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message.'Sum of rent assistance payment(s) can’t be greater then Monthly Rent Amount.';
    }
}
