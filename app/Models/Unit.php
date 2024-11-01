<?php

namespace App\Models;

use Carbon\Carbon;
use Auth;
use App\Services\UniqueLinkService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private $ulService;

    protected $fillable = [
        'name',
        'square',
        'bedrooms',
        'full_bathrooms',
        'half_bathrooms',
        'description',
        'internal_notes',
        'additional_requirements',
        'available_date',
        'duration',
        'monthly_rent',
        'security_deposit',
        'minimum_credit',
        'minimum_income',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $imageUrl = '';
    public $totalExpenses = null;
    public $totalExpenses12 = null;
    public $totalIncome = null;
    public $totalIncome12 = null;

    public function image() {
        return $this->hasOne('App\Models\File', 'id', 'img');
    }

    public function gallery() {
        return $this->belongsToMany(
            'App\Models\File',
            'unit_image_gallery',
            'unit_id',
            'file_id'
        )->orderBy('sort');
    }

    protected $appends = [
        'unique_link',
        'custom_created_at'
    ];

    public static function boot()
    {
        parent::boot();


        self::created(function ($model) {

//            dd($model->property->full_address);

            $model->link()->create([
                'model_id' => $model->id,
                'model_type' => self::class,
                'link' => UniqueLinkService::build($model)
            ]);
        });
    }

    public function leases() {
        return $this->hasMany(
            'App\Models\Lease'
        )->orderBy('created_at','desc')->withTrashed();
    }

    public function amenities() {
        return $this->hasMany('App\Models\Amenities');
    }

    public function maintenanceRequests() {
        return $this->hasMany(
            'App\Models\MaintenanceRequest'
        );
    }

    public function haveActiveLeases() {
        foreach ($this->leases as $lease) {
            if (empty($lease->deleted_at) || $lease->deleted_at->gte(Carbon::now()))
                return true;
        }
        return false;
    }

    public function property() {
        return $this->hasOne(
            'App\Models\Property',
            'id',
            'property_id'
        );
    }

    public function expenses() {
        return $this->hasMany(
            'App\Models\Expenses'
        );
    }

    public function totalExpenses()
    {
        if ($this->totalExpenses === null){
            $this->totalExpenses = number_format(Expenses::where('unit_id',$this->id)->sum('amount'), 2, '.', '');
        }
        return $this->totalExpenses;
    }

    public function totalExpenses12()
    {
        if ($this->totalExpenses12 === null){
            $this->totalExpenses12 = number_format(Expenses::where('unit_id',$this->id)
                ->where('expense_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'))
                ->sum('amount'), 2, '.', '');
        }
        return $this->totalExpenses12;
    }

    public function totalIncome()
    {
        if ($this->totalIncome === null){
            $query = DB::table('invoices');
            $query->join('leases', 'leases.id', '=', 'invoices.base_id');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->where('invoices.is_lease_pay', '=', 1);
            $query->where('units.id',$this->id);
            $invoices = $query->pluck('invoices.id');
            $this->totalIncome = number_format(Payment::whereIn('invoice_id',$invoices)->sum('amount'), 2, '.', '');
        }
        return $this->totalIncome;
    }

    public function totalIncome12()
    {
        if ($this->totalIncome12 === null){
            $query = DB::table('invoices');
            $query->join('leases', 'leases.id', '=', 'invoices.base_id');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->where('invoices.is_lease_pay', '=', 1);
            $query->where('units.id',$this->id);
            $query->where('due_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'));
            $invoices = $query->pluck('invoices.id');
            $this->totalIncome12 = number_format(Payment::whereIn('invoice_id',$invoices)->sum('amount'), 2, '.', '');
        }
        return $this->totalIncome12;
    }

    public function getStatusAttribute() {
        $openedLeases = Lease::where('unit_id', $this->id)->whereNull('deleted_at')->count();
        if ($openedLeases !== 0) {
            return 0; // occupied
        }

        return 1; // vacant
    }

    public function isOccupied() {
        return Lease::where([
            ['deleted_at', '=', null],
            ['unit_id', '=', $this->id],
        ])->count() > 0;
    }

    public function imageUrl()
    {
        if ($this->imageUrl === ''){
            $this->imageUrl = $this->img ? url('storage/property/' . $this->property_id . '/' . $this->id . '/' . ($this->image->filename)) : null;
        }
        return $this->imageUrl;
    }

    public function link() {
        return $this->morphOne(UniqueLink::class, 'model');
    }

    public function getPublicLinkAttribute()
    {
        return route('view/unique_link', ['unique_link' => $this->unique_link ]);
    }

    public function getUniqueLinkAttribute() {
        $linkInstance = $this->link;
        return !empty($linkInstance) ? $linkInstance->link : null;
    }

    public function getCustomCreatedAtAttribute() {
        return Carbon::parse($this->created_at)->format('m/d/Y');
    }

    public function getDepositAttribute()
    {
        $deposit = 0;
        foreach ($this->leases as $l) {
            $deposit += $l->deposit;
        }
        return number_format($deposit, 2, '.', '');
    }

    public function getOutstandingAttribute()
    {
        $outstanding = 0;
        foreach ($this->leases as $l) {
            $outstanding += $l->outstanding;
        }
        return number_format($outstanding, 2, '.', '');
    }
}
