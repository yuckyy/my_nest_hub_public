<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\Register;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\File;
use App\Models\Application;
use App\Models\MaintenanceRequest;
use App\Models\UserAddon;
use App\Models\UserSetting;
use App\Models\Addon;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'email_verified_at',
        'customer_id',
        'last_login_at',
        'last_login_ip',
        'intended_url'
    ];

    public $photoUrl = '';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            'App\Models\Role',
            'users_roles',
            'user_id',
            'role_id'
        );
    }

    public function properties()
    {
        return $this->hasMany('App\Models\Property');
    }
    public function propertiesArchive()
    {
        return $this->hasMany('App\Models\Property');
    }
    public function financialAccounts()
    {
        //TODO Revising...
        //looks correct for tenant
        //TODO check in replace account procedure
        if ($this->isTenant()) {
            return $this->hasMany('App\Models\Financial');
        }
        return $this->hasMany('App\Models\Financial')->where('connected',1);
    }

    public function financialAllAccounts()
    {
        return Financial::where('user_id',$this->id)
            ->where(function ($query) {
                $query->where('connected', '=', 1)
                    ->orWhere('finance_type', '=', 'dwolla_target')
                    ->orWhere('finance_type', '=', 'card')
                    ->orWhere('finance_type', '=', 'bank');
            })->get();
        //->where('connected',1)->get();
    }

    public function financialCollectAccounts()
    {
        //Didn't used yet
        return Financial::where('user_id',$this->id)
            ->where(function ($query) {
                $query->where('finance_type','stripe_account')
                    ->where('connected',1)
                    ->orWhere('finance_type','paypal')->get();
            })->get();
    }

    public function financialCollectRecurringAccounts()
    {
        /*
        return Financial::where('user_id',$this->id)
            ->where(function ($query) {
                $query->whereIn('finance_type',['stripe_account', 'dwolla_target'])
                    ->where('connected',1)
                    ->get();
            })->get();
        */
        return Financial::where('user_id',$this->id)
            ->where(function ($query) {
                $query->where('finance_type', 'stripe_account')
                    ->where('connected',1)
                    ->orWhere('finance_type', '=', 'dwolla_target');
            })->get();
    }


    public function financialStripeAccounts()
    {
        return Financial::where('user_id',$this->id)->where('finance_type','stripe_account')->where('connected',1)->get();
    }

    public function financialDwollaAccounts()
    {
        return Financial::where('user_id',$this->id)->where('finance_type','dwolla_target')->where('connected',1)->get();
    }

    public function financialSubscribeAccounts()
    {
        return Financial::where('user_id',$this->id)
            ->where(function ($query) {
                $query->where('finance_type', '=', 'card')
                    ->orWhere('finance_type', '=', 'bank');
            })->get();
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function leases() {
        return $this->hasMany(
            'App\Models\Lease','email','email'
        )->orderBy('created_at','desc')->withTrashed();
    }

    public function sharedApplications()
    {
        return $this->belongsToMany(
            Application::class,
            'applications_users',
            'user_id',
            'application_id'
        );
    }

    public function plans()
    {
        return $this->hasMany('App\Models\UserPlan');
    }

    public function addons()
    {
        return $this->hasMany('App\Models\UserAddon');
    }

    public function userSettings()
    {
        return $this->hasMany('App\Models\UserSetting');
    }

    public function userIdentity()
    {
        return $this->hasOne('App\Models\UserAddon');
    }

    public function preferences()
    {
        return $this->hasOne('App\Models\Preference');
    }

    public function vacantUnitsCount() {
        $vacantUnitsCount = 0;
        foreach ($this->properties as $property){
            foreach ($property->units as $unit) {
                if (!$unit->isOccupied()) {
                    $vacantUnitsCount++;
                }
            }
        }
        //TODO make it with sql (exclude join with soft delete??)
        /*
        $vacantUnitsCount = Unit::leftJoin('properties', 'properties.id', '=', 'units.property_id')
            ->leftJoin('leases', 'leases.unit_id', '=', 'units.id')
            ->whereNull('leases.unit_id')
            ->where('properties.user_id', '=', $this->id)
            ->count();
        $vacantUnitsCount2 = Unit::leftJoin('properties', 'properties.id', '=', 'units.property_id')
            ->leftJoin('leases', 'leases.unit_id', '=', 'units.id')
            ->whereNotNull('leases.deleted_at')
            ->where('properties.user_id', '=', $this->id)
            ->count();
        */
        return $vacantUnitsCount;
    }


    public function photoUrl()
    {
        if ($this->photoUrl === ''){
            $this->photoUrl = $this->photo_file_id ? url('storage/profile/' . (File::find($this->photo_file_id)->filename)) : null;
        }
        return $this->photoUrl;
    }

    public function fullName()
    {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
    /**
     * Send the password reset notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new Register());
    }

    public function getFullNameAttribute() {
        return $this->name." ".$this->lastname;
    }

    public function getUnitsCountAttribute()
    {
        if ($this->isLandlord() || $this->isPropManager()) {
            return count($this->getUnits());
        }
    }

    public function getUnits()
    {
        if (!$this->isTenant()) {
            return Unit::whereIn('property_id',$this->properties->pluck('id'))->get();
        }
        $uIds = Lease::where('email', $this->email)->pluck('unit_id')->toArray();
        return Unit::whereIn('id',$uIds)->get();
    }

    public function isAdmin()
    {
        if (strtolower($this->roles[0]->name) == "admin") {
            return true;
        }
        return false;
    }

    public function isLandlord()
    {
        if (strtolower($this->roles[0]->name) == "landlord") {
            return true;
        }
        return false;
    }

    public function isPropManager()
    {
        if (strtolower($this->roles[0]->name) == "property manager") {
            return true;
        }
        return false;
    }

    public function isTenant()
    {
        if (strtolower($this->roles[0]->name) == "tenant") {
            return true;
        }
        return false;
    }

    public function getNewMaintenanceRequestsCount()
    {
        $query = MaintenanceRequest::query();

        if ($this->isLandlord() || $this->isPropManager()) {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('properties.user_id', $this->id);
        } else {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('leases', 'leases.unit_id', '=', 'units.id');
            $query->where('leases.email', $this->email);
        }
        $query->where('maintenance_requests.archived', 0);

        $query->where('maintenance_requests.status_id', '1');
        $count = $query->count();

        return $count;
    }

    public function getDepositAttribute()
    {
        $deposit = 0;
        if($this->isTenant()){
            foreach ($this->leases as $l) {
                $deposit += $l->deposit;
            }
        } elseif ($this->isLandlord()){
            foreach ($this->properties as $property) {
                foreach ($property->units as $unit) {
                    foreach ($unit->leases as $l) {
                        $deposit += $l->deposit;
                    }
                }
            }
        } elseif($this->isPropManager()){
            //TODO
        }
        return financeFormat($deposit);
    }

    public function getDeposit12Attribute()
    {
        $deposit = 0;
        if($this->isTenant()){
            foreach ($this->leases as $l) {
                $deposit += $l->deposit12;
            }
        } elseif ($this->isLandlord()){
            foreach ($this->properties as $property) {
                foreach ($property->units as $unit) {
                    foreach ($unit->leases as $l) {
                        $deposit += $l->deposit12;
                    }
                }
            }
        } elseif($this->isPropManager()){
            //TODO
        }
        return financeFormat($deposit);
    }


    public function getOutstandingAttribute()
    {
        $outstanding = 0;
        if($this->isTenant()){
            foreach ($this->leases as $l) {
                $outstanding += $l->outstanding;
            }
        } elseif ($this->isLandlord()){
            foreach ($this->properties as $property) {
                foreach ($property->units as $unit) {
                    foreach ($unit->leases as $l) {
                        $outstanding += $l->outstanding;
                    }
                }
            }
        } elseif($this->isPropManager()){
            //TODO
        }
        return financeFormat($outstanding);
    }

    public function getOutstanding12Attribute()
    {
        $outstanding = 0;
        if($this->isTenant()){
            foreach ($this->leases as $l) {
                $outstanding += $l->outstanding12;
            }
        } elseif ($this->isLandlord()){
            foreach ($this->properties as $property) {
                foreach ($property->units as $unit) {
                    foreach ($unit->leases as $l) {
                        $outstanding += $l->outstanding12;
                    }
                }
            }
        } elseif($this->isPropManager()){
            //TODO
        }
        return financeFormat($outstanding);
    }

    public function nextFinanceOrder()
    {
        if ($lastf = Financial::where('user_id',$this->id)->orderBy('finance_order','desc')->first()) {
            return $lastf->finance_order + 1;
        }
        return 0;
    }

    public function hasAddon($name)
    {
        $addon = Addon::where(['name' => $name ])->first();
        if (!empty($addon)) {
            $ua = UserAddon::where(['user_id' => $this->id, 'addon_id' => $addon->id])->first();
            if (!empty($ua)) {
                return true;
            }
        }
        return false;
    }

    public function activePlan()
    {
        return $this->plans()->whereNull('deleted_at')->first();
    }

    public function availableUnitsCount()
    {
        if (!$this->activePlan()) {
            //free trial
            return freeTrialParams('max_units') - $this->units_count;
        }
        return $this->activePlan()->subscriptionPlan->max_units - $this->units_count;
    }

    public function getSettingsValue($key)
    {
        $settings = $this->userSettings->where('key',$key)->first();
        return $settings->value ?? null;
    }

    public function expensesTypes()
    {
        return ExpenseType::where("user_id",$this->id)->orWhereNull("user_id")->orderBy("name")->get();
    }

    public function userIdentityStatus()
    {
        $identity = UserIdentity::where('user_id', $this->id)->first();
        return $identity->status ?? null;
    }

}
