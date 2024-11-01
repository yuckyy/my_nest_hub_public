<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class ResidenceHistory extends Model
{
    //
    protected $fillable = [
        'start_date', 'end_date', 'current', 'address',
        'city', 'state_id', 'application_id'
    ];

    protected $appends = [
        'full_address',
        'custom_start_date',
        'custom_end_date'
    ];

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function getFullAddressAttribute() {
        return $this->address . ($this->address ? ', ' : '') . $this->city . ($this->city ? ', ' : '') . ($this->state ? $this->state->name : '');
    }

    public function getCustomStartDateAttribute() {
        return !empty($this->start_date) ? Carbon::parse($this->start_date)->format('m/d/Y') : "";
    }

    public function getCustomEndDateAttribute() {
        return !empty($this->end_date) ? Carbon::parse($this->end_date)->format('m/d/Y') : Carbon::parse(now())->format('m/d/Y');
    }
}
