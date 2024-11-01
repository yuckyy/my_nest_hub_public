<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    protected $fillable = [
        'base_id', 'is_lease_pay', 'is_late_fee', 'due_date', 'description', 'amount', 'pay_month'
    ];

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function base() {
        if ($this->is_lease_pay) {
            return $this->belongsTo(Lease::class, 'base_id', 'id')->withTrashed();
        }

        //TODO we don't have soft deletes under the Bill yet
        //return $this->belongsTo(Bill::class, 'base_id', 'id')->withTrashed();
        return $this->belongsTo(Bill::class, 'base_id', 'id');
    }

    public function getLeaseAttribute() {
        if ($this->base instanceof Lease) {
            $lease = $this->base;
        } else {
            $lease = $this->base->lease;
        }

        return $lease;
    }

    public function leaseWithTrashed() {
        if ($this->is_lease_pay) {
            $lease = Lease::where('id', $this->base_id)->withTrashed()->first();
        } else {
            $bill = Bill::where('id', $this->base_id)->first();
            $lease = Lease::where('id', $bill->lease_id)->withTrashed()->first();
        }
        return $lease;
    }

    public function getFullNameAttribute() {
        return $this->getLeaseAttribute()->full_name;
    }

    public function getEmailAttribute() {
        return $this->getLeaseAttribute()->email;
    }

    public function getFullAddressAttribute() {
        return $this->getLeaseAttribute()->unit->property->full_address;
    }

    public function getBalanceAttribute() {
        return number_format($this->payed - $this->amount, 2, '.', '');
    }

    public function getBillAmountAttribute() {
        return number_format($this->amount - $this->payed, 2, '.', '') > 0 ? number_format($this->amount - $this->payed, 2, '.', '') : 0;
    }

    public function getPayedAttribute()
    {
        return number_format($this->payments->sum('amount'), 2, '.', '');
    }
}
