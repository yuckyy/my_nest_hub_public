<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;
use Auth;
use Illuminate\Notifications\Notifiable;
use App\Models\Document;

class Lease extends Model
{
    //
    use SoftDeletes, Notifiable;

    protected $appends = [
        'end_date',
        'start_date',
        'full_name'
    ];

    protected $fillable = [
        'firstname', 'lastname', 'email', 'phone',
        'unit_id', 'start_date', 'end_date',
        'monthly_due_date',
        'amount', 'section8', 'military', 'other',
        'prorated_rent_due', 'prorated_rent_amount',
        'late_fee_day', 'late_fee_amount', 'security_deposit',
        'security_amount', 'created_at', 'updated_at',
        'deleted_at', 'application_id'
    ];


    public function bills() {
        return $this->hasMany(
            'App\Models\Bill'
        );
    }

    public function documents() {
        return $this->hasMany(
            'App\Models\Document'
        );
    }

    public function moveIns() {
        return $this->hasMany(
            'App\Models\MoveIn'
        );
    }

    public function unit() {
        return $this->hasOne(
            'App\Models\Unit',
            'id',
            'unit_id'
        );
    }

    public function application() {
        return $this->belongsTo(Application::class);
    }

    public function getFullNameAttribute() {
        return $this->firstname." ".$this->lastname;
    }

    public function getCustomStartDateAttribute() {
        return Carbon::parse($this->start_date)->format('m/d/Y');
    }

    public function getCustomEndDateAttribute() {
        return !empty($this->end_date) ? Carbon::parse($this->end_date)->format('m/d/Y') : "";
    }

    //public function getEndDateAttribute() {
    //    return !empty($this->end_date) ? $this->end_date : "";
    //}

    //public function getStartDateAttribute() {
    //    return !empty($this->start_date) ? $this->start_date : "";
    //}

    public function getTenantAttribute() {
        return User::where('email', $this->email)->first() ? User::where('email', $this->email)->first()->full_name : '';
    }

    public function tenantLastLogin() {
        return User::where('email', $this->email)->first() ? User::where('email', $this->email)->first()->last_login_at : null;
    }

    public function getLandlordAttribute() {
        return $this->unit->property->user;
    }

    public function landlordLinkedFinance()
    {
        $fu = FinanceUnit::where([['unit_id',$this->unit->id],['user_id',$this->landlord->id]])->first();

        if ($fu && $fu->finance && $fu->finance->connected == 1) {
            return $fu;
        }
        return false;
    }

    public function landlordLinkedFinanceStripe()
    {
        $fu = FinanceUnit::where([['unit_id',$this->unit->id],['user_id',$this->landlord->id]])->first();

        if ($fu && $fu->finance && $fu->finance->connected == 1 && $fu->finance->finance_type == 'stripe_account' ) {
            return $fu;
        }
        return false;
    }

    public function landlordLinkedFinanceDwolla()
    {
        $fu = FinanceUnit::where([['unit_id',$this->unit->id],['user_id',$this->landlord->id]])->first();

        if ($fu && $fu->finance && $fu->finance->connected == 1 && $fu->finance->finance_type == 'dwolla_target' ) {
            return $fu;
        }
        return false;
    }

    public function landlordPayPalFinancial()
    {
        $landlord = $this->unit->property->user;
        return Financial::where([['finance_type','paypal'],['user_id',$landlord->id]])->first();
    }

    public function tenantLinkedFinance()
    {
        return FinanceUnit::where([['unit_id',$this->unit->id],['user_id',Auth::user()->id]])->first();
    }

    public function getTotalMonthlyAttribute() {
        return number_format($this->amount + $this->total_collect_bills, 2, '.', '');
    }

    public function getTotalAssistanceAttribute() {
        return number_format($this->section8 + $this->military + $this->other, 2, '.', '');
    }

    public function getTotalByTenantAttribute() {
        return number_format($this->amount + $this->total_collect_bills - $this->total_assistance, 2, '.', '');
    }

    public function getTotalCollectBillsAttribute() {
        return number_format($this->bills->where('bill_mode','collect')->sum('value'), 2, '.', '');
    }

    public function getTotalAdditionalBillsAttribute() {
        return number_format($this->bills->where('bill_mode','additional')->sum('value'), 2, '.', '');
    }

    public function getBalanceAttribute() {
        return number_format($this->deposit - $this->invoices()->sum('amount'), 2, '.', '');
    }

    public function getDepositAttribute()
    {
        $invoices = $this->invoices()->pluck('id');

        return number_format(Payment::whereIn('invoice_id',$invoices)->sum('amount'), 2, '.', '');
    }

    public function getOutstandingAttribute()
    {
        return number_format($this->invoices()->sum('amount') - $this->deposit, 2, '.', '');
    }

    public function invoices()
    {
        return Invoice::where(function ($q) {
                    $q->where([['base_id',$this->id],['is_lease_pay',1]])
                        ->orWhere(function ($d) {
                            $d->whereIn('base_id',$this->bills->where('bill_mode','additional')->pluck('id'))
                                ->where('is_lease_pay',0);
                        });
                })->orderBy('pay_month','desc');
    }

    public function getDeposit12Attribute()
    {
        $invoices = Invoice::where(function ($q) {
            $q->where([['base_id',$this->id],['is_lease_pay',1]])
                ->orWhere(function ($d) {
                    $d->whereIn('base_id',$this->bills->where('bill_mode','additional')->pluck('id'))
                        ->where('is_lease_pay',0);
                });
        })->where('due_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'))->pluck('id');

        return number_format(Payment::whereIn('invoice_id',$invoices)->sum('amount'), 2, '.', '');
    }

    public function getOutstanding12Attribute()
    {
        $invoices = Invoice::where(function ($q) {
            $q->where([['base_id',$this->id],['is_lease_pay',1]])
                ->orWhere(function ($d) {
                    $d->whereIn('base_id',$this->bills->where('bill_mode','additional')->pluck('id'))
                        ->where('is_lease_pay',0);
                });
        })->where('due_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'));

        return number_format($invoices->sum('amount') - $this->deposit12, 2, '.', '');
    }


    public static function lateLeases($lanlord = null)
    {
        //todo remove it someday (see comment in CreateInvoices)
        // get not payed lease invoices
        $lIds = [];
        $inv = Invoice::where([['is_lease_pay',1],['is_late_fee',0]])
                        ->get()
                        ->filter(function($item) {
                            return $item->balance < 0;
                        });

        foreach ($inv as $i) {
            $lIds []= $i->base_id;
        }

        $lateLeases = self::where('late_fee_amount','>',0)
                    ->whereRaw('DAY(DATE_SUB(NOW(), INTERVAL `late_fee_day` DAY)) = monthly_due_date')
                    ->whereIn('id',$lIds)
                    //->whereNull('end_date')
                    ->get();
        if ($lanlord) {
            return $lateLeases->filter(function($item) use ($lanlord) {
                            return $item->lanlord = $lanlord;
                        });
        }
        return $lateLeases;
    }

    public function sharedDocuments() {
        return Document::where(['document_type' => 'shared_document', 'lease_id' => $this->id])->get();
    }

    public function moveInPhotos() {
        return Document::where(['user_id' => Auth::user()->id, 'document_type' => 'move_in_photo', 'lease_id' => $this->id])->get();
    }

    public function moveOutPhotos() {
        return Document::where(['user_id' => Auth::user()->id, 'document_type' => 'move_out_photo', 'lease_id' => $this->id])->get();
    }

}
