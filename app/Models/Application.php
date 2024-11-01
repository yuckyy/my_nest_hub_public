<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Application extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'firstname', 'lastname', 'evicted_or_unlawful',
        'smoke', 'user_id', 'dob', 'email', 'phone',
        'felony_or_misdemeanor', 'refuse_to_pay_rent',
        'unit_id', 'start_date', 'end_date', 'new', 'notes', 'internal_notes'
    ];

    protected $appends = [
        'custom_created_at',
        'custom_d_o_b',
        'full_income'
    ];

    const HAS_MANY_RELATIONS = [
        'incomes', 'amenties',
        'pets', 'employmentAndlIncomes',
        'references', 'residenceHistories'
    ];

    public function leases(){
        return $this->hasMany(Lease::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function additionalIncomes() {
        return $this->hasMany(AdditionalIncome::class);
    }

    public function incomes() {
        return $this->hasMany(AdditionalIncome::class);
    }

    public function amenties() {
        return $this->hasMany(Amenities::class);
    }

    public function residenceHistories() {
        return $this->hasMany(ResidenceHistory::class);
    }

    public function pets() {
        return $this->hasMany(Pets::class);
    }

    public function employmentAndlIncomes() {
        return $this->hasMany(EmploymentAndIncome::class);
    }

    public function references() {
        return $this->hasMany(References::class);
    }

    public function addonScreening() {
        return $this->hasMany(AddonScreening::class);
    }

    public function sharedWithUsers()
    {
        return $this->belongsToMany(
            User::class,
            'applications_users',
            'application_id',
            'user_id'
        );
    }

    public function applicationDocuments() {
        return $this->hasMany(
            'App\Models\ApplicationDocument'
        );
    }

    public function getCustomCreatedAtAttribute() {
        return Carbon::parse($this->created_at)->format('m/d/Y');
    }

    public function getCustomDOBAttribute() {
        return Carbon::parse($this->dob)->format('m/d/Y');
    }

    public function is_new(){
        $d = DB::table('applications_users')
            ->where('application_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->select('is_new')
            ->first();
        return $d->is_new ?? false;
    }

    public function applied_time(){
        $d = DB::table('applications_users')
            ->where('application_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->select('applied_at')
            ->first();
        return Carbon::parse( (!empty($d->applied_at) ? $d->applied_at : $this->created_at) )->format('M d, Y. g:ia');
    }

    public function shared_with(){
        $s = DB::table('applications_users')
            ->where('application_id', $this->id)
            ->select('applied_at', 'user_id')
            ->get();
        $shared = [];
        foreach($s as $item){
            $user = User::find($item->user_id);
            $shared[$user->email] = Carbon::parse($item->applied_at)->format('M d, Y. g:ia');
        }
        return $shared;
    }

    public function getTenantByEmail(){
        return User::where("email",$this->email)->first();
    }

    public function getTenantPhotoByEmail(){
        $tenant = User::where("email",$this->email)->first();
        return $tenant ? $tenant->photoUrl() : null;
    }

    public function  getFullIncomeAttribute() {

        $income = 0;
        if (!$this->incomes->isEmpty())
            foreach ($this->incomes as $additionalIncome)
                $income += $additionalIncome->amount;

        if (!$this->employmentAndlIncomes->isEmpty())
            foreach ($this->employmentAndlIncomes as $additionalIncome)
                $income += $additionalIncome->income;

        return $income;
    }
}
