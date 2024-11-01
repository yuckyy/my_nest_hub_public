<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'img',
        'name',
        'modifier',
        'street',
        'home',
        'zip',
    ];

    protected $appends = [
        'full_address'
    ];

    public $status = -1;
    public $occupied = 0;
    public $vacant = 0;
    public $imageUrl = '';

    public $totalExpenses = null;
    public $totalExpenses12 = null;
    public $totalIncome = null;
    public $totalIncome12 = null;

    public function image() {
        return $this->hasOne('App\Models\File', 'id', 'img');
    }

    public function state() {
        return $this->hasOne('App\Models\State', 'id', 'state_id');
    }

    public function type() {
        return $this->hasOne('App\Models\PropertyType', 'id', 'property_type_id');
    }

    public function units() {
        return $this->hasMany(
            'App\Models\Unit'
        );
    }

    public function maintenanceRequests() {
        return $this->hasMany(
            'App\Models\MaintenanceRequest'
        );
    }

    public function paymentUnits()
    {
        return $this->units()->whereHas('leases')->get();
    }

//    public function activeUnits() {
//        return $this->units->where()
//    }

    public function gallery() {
        return $this->belongsToMany(
            'App\Models\File',
            'image_gallery',
            'property_id',
            'file_id'
        )->orderBy('sort');
    }

    public function status() {
        if ($this->status === -1) {
            $openedLeases = DB::table('leases')
                ->join(
                    'units',
                    function ($join) {
                        $join->on('units.id', '=', 'leases.unit_id');
                    }
                )
                ->where(
                    [
                        ['units.property_id', '=', $this->id],
                        ['leases.deleted_at', '=', null],
                    ]
                )
                ->count();
            $units = DB::table('units')->where('units.property_id', '=', $this->id)->where('units.archived', '=', 0)->count();
            if ($units === 0) {
                $this->status = 0;
                return 0; // occupied
            }

            if ($openedLeases === 0) {
                $this->status = 1;
                return 1; // vacant
            }

            if ($units - $openedLeases === 0) {
                $this->status = 0;
                return 0;
            }
            $this->status = 2;
            return 2; // occupied and vacant
        }

        return $this->status;
    }

    public function occupiedstatus() {

            $openedLeases = DB::table('leases')
                ->join(
                    'units',
                    function ($join) {
                        $join->on('units.id', '=', 'leases.unit_id');
                    }
                )
                ->where(
                    [
                        ['units.property_id', '=', $this->id],
                        ['leases.deleted_at', '=', null],
                    ]
                )
                ->count();
            $units = DB::table('units')->where('units.property_id', '=', $this->id)->where('units.archived', '=', 0)->count();
            $this->occupied = $units - $openedLeases;

            return $this->occupied;


    }

    public function vacantstatus() {

        $openedLeases = DB::table('leases')
            ->join(
                'units',
                function ($join) {
                    $join->on('units.id', '=', 'leases.unit_id');
                }
            )
            ->where(
                [
                    ['units.property_id', '=', $this->id],
                    ['leases.deleted_at', '=', null],
                ]
            )
            ->count();
        $units = DB::table('units')->where('units.property_id', '=', $this->id)->where('units.archived', '=', 0)->count();
        $occupied = $units - $openedLeases;

        if($openedLeases ===0 ){
            $this->vacant = $units;
        }else{
            $this->vacant = $units - $occupied;
        }

        return $this->vacant;


}


    public function imageUrl()
    {
        if ($this->imageUrl === ''){
            $this->imageUrl = $this->img ? url('storage/property/' . $this->id . '/' . ($this->image->filename)) : null;
        }
        return $this->imageUrl;
    }

    public function icon()
    {
        switch ($this->type->name){
            case "Apartment":
                $icon = '<i class="fal fa-building"></i>';
                break;
            case "Single family home":
                $icon = '<i class="fal fa-home"></i>';
                break;
            case "Duplex/Triplex":
                $icon = '<i class="fal fa-store-alt"></i>';
                break;
            case "Mobile/Manufactured home":
                $icon = '<i class="fal fa-store-alt"></i>';
                break;
            case "Dormitory":
                $icon = '<i class="fal fa-building"></i>';
                break;
            case "Commercial":
                $icon = '<i class="fal fa-industry-alt"></i>';
                break;
            case "Townhouse":
                $icon = '<i class="fal fa-home-lg-alt"></i>';
                break;
            default:
                $icon = '<i class="fal fa-home"></i>';
                break;
        }
        return $icon;
    }

    public function getFullAddressAttribute() {
        return $this->address.", ".$this->city.", ". ($this->state ? $this->state->code . ", " : "").$this->zip;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getDepositAttribute()
    {
        $deposit = 0;
        foreach ($this->units as $u) {
            $deposit += $u->deposit;
        }
        return number_format($deposit, 2, '.', '');
    }

    public function getOutstandingAttribute()
    {
        $outstanding = 0;
        foreach ($this->units as $u) {
            $outstanding += $u->outstanding;
        }
        return number_format($outstanding, 2, '.', '');
    }

    public function totalExpenses()
    {
        if ($this->totalExpenses === null){
            $expenses_unit = Expenses::join('units', 'units.id', '=', 'expenses.unit_id')
                ->where('units.property_id',$this->id)
                ->sum('amount');
            $expenses_property = Expenses::where('property_id',$this->id)
                ->sum('amount');
            $this->totalExpenses = number_format($expenses_unit + $expenses_property , 2, '.', '');
        }
        return $this->totalExpenses;
    }

    public function totalExpenses12()
    {
        if ($this->totalExpenses12 === null){
            $expenses_unit = Expenses::join('units', 'units.id', '=', 'expenses.unit_id')
                ->where('units.property_id',$this->id)
                ->where('expense_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'))
                ->sum('amount');
            $expenses_property = Expenses::where('property_id',$this->id)
                ->where('expense_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'))
                ->sum('amount');
            $this->totalExpenses12 = number_format($expenses_unit + $expenses_property , 2, '.', '');
        }
        return $this->totalExpenses12;
    }

    public function totalIncome()
    {
        if ($this->totalIncome === null){
            $query = DB::table('invoices');
            $query->join('leases', 'leases.id', '=', 'invoices.base_id');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('invoices.is_lease_pay', '=', 1);
            $query->where('properties.id',$this->id);
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
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('invoices.is_lease_pay', '=', 1);
            $query->where('properties.id',$this->id);
            $query->where('due_date', '>', Carbon::now()->subMonth(12)->format('Y-m-d'));
            $invoices = $query->pluck('invoices.id');
            $this->totalIncome12 = number_format(Payment::whereIn('invoice_id',$invoices)->sum('amount'), 2, '.', '');
        }
        return $this->totalIncome12;
    }

    public static function deleteProperty($id){
        $property = Property::find($id);

        //Property Image
        if(!empty($property->image)){
            $image = $property->image;
            Storage::delete('public/property/' . $property->id . '/' . $image->filename);
            $image->delete();
            $property->img = null;
            $property->save();
        }

        //Property Gallery
        $gal = DB::table('image_gallery')->where('property_id', $property->id)->get();
        foreach($gal as $item){
            $image = \App\Models\File::find($item->file_id);
            if(!empty($image)) {
                Storage::delete('public/property/' . $property->id . '/gallery/' . $image->filename);
                $image->delete();
            }
        }
        DB::statement(
            'DELETE `image_gallery` FROM `image_gallery` '.
            'WHERE `image_gallery`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        foreach($property->units as $unit){
            //Unit Image
            if(!empty($unit->image)) {
                $image = $unit->image;
                Storage::delete('public/property/' . $property->id . '/' . $unit->id . '/' . $image->filename);
                $image->delete();
                $unit->img = null;
                $unit->save();
            }

            //Unit Gallery
            $gal = DB::table('unit_image_gallery')->where('unit_id', $unit->id)->get();
            foreach($gal as $item){
                $image = \App\Models\File::find($item->file_id);
                if(!empty($image)){
                    Storage::delete('public/property/' . $property->id . '/' . $unit->id . '/gallery/' . $image->filename);
                    $image->delete();
                }
            }
            DB::statement(
                'DELETE `unit_image_gallery` FROM `unit_image_gallery` '.
                'WHERE `unit_image_gallery`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            foreach($unit->leases as $lease) {
                foreach($lease->documents as $document) {
                    //Lease Documents
                    Storage::delete('public/' . $document->filepath);
                    if(!empty($document->thumbnailpath)){
                        Storage::delete('public/' . $document->thumbnailpath);
                    }
                    $document->delete();
                }
                foreach($lease->bills as $bill) {
                    if($bill->file_id){
                        //Bill Documents
                        $document = $bill->file;
                        if(!empty($document)){
                            Storage::delete('public/bill/' . $document->filename);
                            $document->delete();
                        }
                    }
                }
            }

            //maintenance
            foreach($unit->maintenanceRequests as $maintenanceRequest){
                foreach($maintenanceRequest->maintenanceDocuments as $maintenanceDocument){
                    Storage::delete('public/' . $maintenanceDocument->filepath);
                    if(!empty($maintenanceDocument->thumbnailpath)){
                        Storage::delete('public/' . $maintenanceDocument->thumbnailpath);
                    }
                    $maintenanceDocument->delete();
                }
            }

        }

        // Unit based && property based expenses
        $query = DB::table('expenses');
        $query->leftJoin('units', 'units.id', '=', 'expenses.unit_id');
        $query->where('units.property_id', '=', $property->id);
        $query->orWhere('expenses.property_id', '=', $property->id);
        $query->select('expenses.*');
        $expenses = $query->get();
        foreach($expenses as $expense){
            if($expense->file_id){
                $document = \App\Models\File::find($expense->file_id);
                if(!empty($document)){
                    Storage::delete('public/expenses/' . $document->filename);
                    $document->delete();
                }
            }
            //TODO optimize it with the single query someday
            DB::statement(
                'DELETE `expenses` FROM `expenses` '.
                'WHERE `id` =:id',
                ['id' => $expense->id]
            );
        }

        DB::statement( 'DELETE `maintenance_requests` FROM `maintenance_requests` INNER JOIN `units` ON `units`.`id` = `maintenance_requests`.`unit_id` WHERE `units`.`property_id` =:property_id', ['property_id' => $property->id] );

        DB::statement(
            'DELETE `payments` FROM `payments` '.
            'INNER JOIN `invoices` ON `invoices`.`id` = `payments`.`invoice_id` '.
            'INNER JOIN `bills` ON `bills`.`id` = `invoices`.`base_id` '.
            'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `invoices`.`is_lease_pay` = 0 AND `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );
        DB::statement(
            'DELETE `payments` FROM `payments` '.
            'INNER JOIN `invoices` ON `invoices`.`id` = `payments`.`invoice_id` '.
            'INNER JOIN `leases` ON `leases`.`id` = `invoices`.`base_id` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `invoices`.`is_lease_pay` = 1 AND `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        DB::statement(
            'DELETE `invoices` FROM `invoices` '.
            'INNER JOIN `bills` ON `bills`.`id` = `invoices`.`base_id` '.
            'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `invoices`.`is_lease_pay` = 0 AND `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );
        DB::statement(
            'DELETE `invoices` FROM `invoices` '.
            'INNER JOIN `leases` ON `leases`.`id` = `invoices`.`base_id` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `invoices`.`is_lease_pay` = 1 AND `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        DB::statement(
            'DELETE `bills` FROM `bills` '.
            'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        DB::statement(
            'DELETE `leases` FROM `leases` '.
            'INNER JOIN `units` ON `units`.`id` = `leases`.`unit_id` '.
            'WHERE `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        DB::statement( 'DELETE `applications` FROM `applications` INNER JOIN `units` ON `units`.`id` = `applications`.`unit_id` WHERE `units`.`property_id` =:property_id', ['property_id' => $property->id] );

        DB::statement(
            'DELETE `units` FROM `units` '.
            'WHERE `units`.`property_id` =:property_id',
            ['property_id' => $property->id]
        );

        DB::statement(
            'DELETE `properties` FROM `properties` '.
            'WHERE `id` =:property_id',
            ['property_id' => $property->id]
        );


    }

}
