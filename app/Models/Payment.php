<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note',
        'pay_date',
        'transaction_id',
        'amount',
        'invoice_id',
        'finance_id',
        'processing_fee',
        'payment_method',
        'correlation_id'
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function finance() {
        return $this->belongsTo(Financial::class, 'finance_id', 'id')->withTrashed();
    }

    public function getPayMethodAttribute() {
        if($this->payment_method){
            return $this->payment_method;
        }
        if (!$this->finance_id) {
            return "Manually";
        }
        if ($this->finance->finance_type == 'card') {
            return "Credit Card";
        }
        return "Bank Account";
    }

    public function moveToFailedPayments(){
        DB::table('failed_payments')->insert([
            'id' => $this->id,
            'note' => $this->note,
            'pay_date' => $this->pay_date,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'invoice_id' => $this->invoice_id,
            'finance_id' => $this->finance_id,
            'processing_fee' => $this->processing_fee,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'log' => $this->log,
            'correlation_id' => $this->correlation_id,
        ]);
        return true;
    }
}
