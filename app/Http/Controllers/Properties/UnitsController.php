<?php

namespace App\Http\Controllers\Properties;
use App\Http\Requests\ShareUnitEmailRequest;
use App\Notifications\ShareUnit;
use App\Notifications\ShareUnitDatabase;
use App\Repositories\Contracts\UnitRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Factories\AmenitiesFactory;
use Illuminate\Support\Facades\Storage;

class UnitsController extends Controller
{
    //
    private $unitr;
    private $userr;
    private $ar;

    public function __construct(UnitRepositoryInterface $unitr, UserRepositoryInterface $userr, ApplicationsRepositoryInterface $ar) {
        $this->unitr = $unitr;
        $this->userr = $userr;
        $this->ar = $ar;
    }

    public function edit(Request $request,$id) {
        if ($id === null) {
            abort(404);
        }

        $unit = Unit::where('id', $id)->first();
        $unitID = ['unit_id'=>$unit->id];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + ['unit_id' => $unitID]);
        $applicationsCount = $applicationsData['applications']->count();
//        var_dump($applicationsCount);
//        die;
        return view(
            'properties.units.edit',
            [
                'applicationsCount' => $applicationsCount,
                'unit' => $unit,
                'canDelete' => $unit->property->units->count() > 1,
            ]
        );
    }


    public function editSave(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }

        $request->validate(
            [
                'name' => 'required|string|min:1',
                // 'square' => 'required|numeric|min:1',
                'square' => 'nullable|numeric|min:1',
                'bedrooms' => 'required|integer|min:1',
                'full_bathrooms' => 'required|integer|min:0',
                'half_bathrooms' => 'required|integer|min:0',
            ]
        );

        $unit = Unit::where('id', $id)->first();

        $unit->name = $request->get('name') ?? '';
        $unit->square = $request->get('square') ?? null;
        $unit->bedrooms = $request->get('bedrooms') ?? '';
        $unit->full_bathrooms = $request->get('full_bathrooms') ?? '';
        $unit->half_bathrooms = $request->get('half_bathrooms') ?? '';
        $unit->description = $request->get('description') ?? '';
        $unit->internal_notes = $request->get('internal_notes') ?? '';
        $unit->save();

        return redirect()->route('properties/units/edit', ['id' => $unit->id])->with('success','Unit "' . $unit->name . '" has been updated.');
    }

    public function share(int $unit_id,Request $request) {
        $unit = $this->unitr->getById($unit_id);
        $unitID = ['unit_id'=>$unit_id];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
        $applicationsCount = $applicationsData['applications']->count();

        return view(
            'properties.units.share',
            [
                'applicationsCount' => $applicationsCount,
                'unit' => $unit,
                'structures' => AmenitiesFactory::get($unit_id),
            ]
        );
    }

    public function editAmenitiesSave(Request $request, $unit) {
        $data = $request->all();
        unset($data['_token']);
        AmenitiesFactory::set($unit, $data);
        $unitObject = Unit::find($unit);
        return redirect()->route('properties/units/share', ['unit' => $unitObject])->with('success','Unit "'.$unitObject->name.'" has been updated.');
    }

    public function editTermsSave(Request $request, $unit) {

        if ($unit === null) {
            abort(404);
        }

        $request->validate(
            [
                'description' => 'max:65000',
                'additional_requirements' => 'max:65000',
                'duration' => 'nullable|numeric',
                'monthly_rent' => 'string|nullable|max:15',
                'security_deposit' => 'string|nullable|max:15',
                'minimum_credit' => 'string|nullable|max:15',
                'minimum_income' => 'string|nullable|max:15',
            ]
        );

        $unitObject = Unit::find($unit);

        $unitObject->available_date = $request->get('available_date') ?? null;
        $unitObject->description = $request->get('description') ?? null;
        $unitObject->additional_requirements = $request->get('additional_requirements') ?? null;
        $unitObject->duration = $request->get('duration') ?? null;
        $unitObject->monthly_rent = $request->get('monthly_rent') ? (float) str_replace(",","",$request->get('monthly_rent')) : null;
        $unitObject->security_deposit = $request->get('security_deposit') ? (float) str_replace(",","",$request->get('security_deposit')) : null;
        $unitObject->minimum_credit = $request->get('minimum_credit') ? (float) str_replace(",","",$request->get('minimum_credit')) : null;
        $unitObject->minimum_income = $request->get('minimum_income') ? (float) str_replace(",","",$request->get('minimum_income')) : null;
        $unitObject->save();

        return redirect()->route('properties/units/share', ['unit' => $unitObject])->with('success','Unit "'.$unitObject->name.'" has been updated.');
    }

    public function postShare(int $unit, ShareUnitEmailRequest $request) {
        $landlord = $this->userr->getById(Auth::id());
        $tenant = $this->userr->getByEmail(trim($request->email));
        $unit = $this->unitr->getById($unit);
        if (empty($tenant)) {
            $tenant = trim($request->email);
            Notification::route('mail', $tenant)->notify(new ShareUnit($landlord, $unit->property, $unit, $tenant));
        } else {
            $tenant->notify(new ShareUnitDatabase($landlord, $unit->property, $unit, $tenant));
        }
    }

    public function media($id,Request $request) {
        if ($id === null) {
            abort(404);
        }
//        $unit = Unit::where('id', $id)->first();
        $unitID = ['unit_id'=>$id];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
        $applicationsCount = $applicationsData['applications']->count();

        $unit = Unit::where('id', $id)->first();
        $property = $unit->property;

        return view(
            'properties.units.media',
            [
                'applicationsCount' => $applicationsCount,
                'unit' => $unit,
                'property' => $property,
            ]
        );
    }

    public function tenants(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }

        $unit = Unit::where('id', $id)->first();
        $search= $request->get('search');

        $query = Lease::query()->withTrashed();
        $query->where('leases.unit_id', $unit->id);
        if(!empty($request->get('search'))) {
            $query->where(function ($query) use ($search) {
                $firstname = explode(' ', $search)[0];
                $lastname = explode(' ', $search)[1] ?? '';
                if ($lastname) {
                    return $query->where('firstname','like', '%' . $firstname . '%')
                        ->where('lastname','like', '%' . $lastname . '%');
                } else {
                    return $query->where('email','like', '%' . $search . '%')
                        ->orWhere('firstname','like', '%' . $search . '%')
                        ->orWhere('lastname','like', '%' . $search . '%');
                }
            });
        }
        $query->select(['leases.*']);

        //Sorting
        $availableColumns = [
            'leases.firstname',
            'leases.lastname',
            'leases.email',
            'leases.phone',
            'leases.start_date',
            'leases.end_date',
            'leases.amount',
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        }

        // Sorting by attributes
        $availableAttributes = [
            'balance',
        ];
        if (in_array($column, $availableAttributes)) {
            if ($request->get('order') === 'asc') {
                $allLeasesSorted = $query->get()->sortBy($column)->pluck('id')->toArray();
            } else {
                $allLeasesSorted = $query->get()->sortByDesc($column)->pluck('id')->toArray();
            }
            $ids = implode(',', $allLeasesSorted);

            $leases = $query->orderByRaw(\DB::raw("FIELD(id, ".$ids." )"));
        }

        $leases = $query->paginate(50);

        return view(
            'properties.units.tenants',
            [
                'leases' => $leases,
                'search' => $search,
                'unit' => $unit,
            ]
        );

    }

    public function operationsSave(Request $request, $id)
    {
        $unit = Unit::where('id', $id)->first();
        if (!$unit) {
            abort(404);
        }
        $property = $unit->property;
        if(Auth::user()->id != $property->user_id){
            abort(404);
        }

        if($request->archive == 1){
            //check for active leases
            $query = DB::table('leases');
            $query->where('leases.unit_id', '=', $unit->id);
            $query->whereNull('leases.deleted_at');
            if($query->count() > 0){
                $lease = $query->first();
                $endLeaseUrl = route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id]).'#end_lease_anchor';
                return redirect()->route('properties/units/edit', ['unit' => $unit->id])->with('error','This Unit can\'t be archived because it has an active Lease. ' . '<a href="'.$endLeaseUrl.'" class="text-danger"><b><u>Click here</u></b></a>' . ' to end this active lease.');
            }

            // archive unit, applications and maintenance.
            // leases, bills, invoices and payments will not be visible then

            DB::statement( 'UPDATE `maintenance_requests`, `units` SET `maintenance_requests`.`archived` = 1 WHERE `maintenance_requests`.`unit_id` = :unit_id', ['unit_id' => $unit->id] );
            DB::statement( 'UPDATE `applications`, `units` SET `applications`.`archived` = 1 WHERE `applications`.`unit_id` = :unit_id', ['unit_id' => $unit->id] );
            DB::statement( 'UPDATE `units` SET `archived` = 1 WHERE `id` =:unit_id', ['unit_id' => $unit->id] );

            return redirect()->route('properties/edit', ['property' => $property->id])->with('success','Unit successfully archived.');
        }

        if($request->delete == 1){
            //check for active leases
            $query = DB::table('leases');
            $query->where('leases.unit_id', '=', $unit->id);
            $query->whereNull('leases.deleted_at');
            if($query->count() > 0){
                $lease = $query->first();
                $endLeaseUrl = route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id]).'#end_lease_anchor';
                return redirect()->route('properties/units/edit', ['unit' => $unit->id])->with('error','This Unit can\'t be deleted because it has an active Lease. ' . '<a href="'.$endLeaseUrl.'" class="text-danger"><b><u>Click here</u></b></a>' . ' to end this active lease.');
            }

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

            foreach($unit->maintenanceRequests as $maintenanceRequest){
                foreach($maintenanceRequest->maintenanceDocuments as $maintenanceDocument){
                    Storage::delete('public/' . $maintenanceDocument->filepath);
                    if(!empty($maintenanceDocument->thumbnailpath)){
                        Storage::delete('public/' . $maintenanceDocument->thumbnailpath);
                    }
                    $maintenanceDocument->delete();
                }
            }

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

            // Unit based expenses
            $query = DB::table('expenses');
            $query->where('expenses.unit_id', '=', $unit->id);
            $query->select('expenses.*');
            $expenses = $query->get();
            foreach($expenses as $expense){
                if($expense->file_id){
                    $document = File::find($expense->file_id);
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

            DB::statement( 'DELETE `maintenance_requests` FROM `maintenance_requests` WHERE `maintenance_requests`.`unit_id` =:unit_id', ['unit_id' => $unit->id] );

            DB::statement(
                'DELETE `payments` FROM `payments` '.
                'INNER JOIN `invoices` ON `invoices`.`id` = `payments`.`invoice_id` '.
                'INNER JOIN `bills` ON `bills`.`id` = `invoices`.`base_id` '.
                'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
                'WHERE `invoices`.`is_lease_pay` = 0 AND `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );
            DB::statement(
                'DELETE `payments` FROM `payments` '.
                'INNER JOIN `invoices` ON `invoices`.`id` = `payments`.`invoice_id` '.
                'INNER JOIN `leases` ON `leases`.`id` = `invoices`.`base_id` '.
                'WHERE `invoices`.`is_lease_pay` = 1 AND `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            DB::statement(
                'DELETE `invoices` FROM `invoices` '.
                'INNER JOIN `bills` ON `bills`.`id` = `invoices`.`base_id` '.
                'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
                'WHERE `invoices`.`is_lease_pay` = 0 AND `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );
            DB::statement(
                'DELETE `invoices` FROM `invoices` '.
                'INNER JOIN `leases` ON `leases`.`id` = `invoices`.`base_id` '.
                'WHERE `invoices`.`is_lease_pay` = 1 AND `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            DB::statement(
                'DELETE `bills` FROM `bills` '.
                'INNER JOIN `leases` ON `leases`.`id` = `bills`.`lease_id` '.
                'WHERE `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            DB::statement(
                'DELETE `leases` FROM `leases` '.
                'WHERE `leases`.`unit_id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            DB::statement( 'DELETE `applications` FROM `applications` WHERE `applications`.`unit_id` =:unit_id', ['unit_id' => $unit->id] );

            DB::statement(
                'DELETE `units` FROM `units` '.
                'WHERE `units`.`id` =:unit_id',
                ['unit_id' => $unit->id]
            );

            return redirect()->route('properties')->with('success','Unit successfully deleted.');
        }
    }

    public function unarchive(Request $request) {
        $unit = Unit::where('id', $request->get('record_id'))->first();
        if (!$unit) {
            abort(404);
        }
        $property = $unit->property;
        if(Auth::user()->id != $property->user_id){
            abort(404);
        }

        //DB::statement( 'UPDATE `maintenance_requests`, `units` SET `maintenance_requests`.`archived` = 1 WHERE `maintenance_requests`.`unit_id` = :unit_id', ['unit_id' => $unit->id] );
        DB::statement( 'UPDATE `applications`, `units` SET `applications`.`archived` = 0 WHERE `applications`.`unit_id` = :unit_id', ['unit_id' => $unit->id] );
        DB::statement( 'UPDATE `units` SET `archived` = 0 WHERE `id` =:unit_id', ['unit_id' => $unit->id] );

        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

}
