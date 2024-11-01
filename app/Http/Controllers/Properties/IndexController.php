<?php

namespace App\Http\Controllers\Properties;

use App\Http\Requests\AddPropertyRequest;
use App\Http\Requests\EditPropertyRequest;
use App\Models\Expenses;
use App\Models\MaintenanceRequest;
use App\Models\Pets;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\PropertiesRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use App\Models\Property;
use App\Models\Unit;
use App\Models\File;
use App\Models\Gallery;
use App\Models\State;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    private $ar;
    private $pr;
    public function __construct(ApplicationsRepositoryInterface $ar, PropertiesRepositoryInterface $pr) {
        $this->ar = $ar;
        $this->pr = $pr;
    }
    //
    public function index(Request $request,$page = 1) {

        $query = Auth::user()->properties();
        $queryArchive = Auth::user()->propertiesArchive();
        $queryArchive->where('archived',1);
        $propertiesArchive = $queryArchive->get();
        if($propertiesArchive == '[]'){
            $propertiesArchive = 0;
        }else{
            $propertiesArchive = 1;
        }
        if (\Request::has('address')) {
            $query->where('address', 'like', '%' . \Request::get('address') . '%');
        }
        if (\Request::has('archived')) {
            $query->where('archived',1);
        } else {
            $query->where('archived',0);
        }
        $properties = $query->get();
        $allMaintenanceCounts = array();
        $allApplicationCountss = array();
        foreach ($properties as $property){
            $property = Auth::user()->properties->where('id', $property->id)->first();
            if (\Request::has('archived_units')) {
                $units = $property->units->where('archived',1)->sortBy('id');
            } else {
                $units = $property->units->where('archived',0)->sortBy('id');
            }
            $query = MaintenanceRequest::query();

            if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
                $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
                $query->join('properties', 'properties.id', '=', 'units.property_id');
                $query->where('properties.user_id', Auth::id());
            } else {
                $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
                $query->join('leases', 'leases.unit_id', '=', 'units.id');
                $query->where('leases.email', Auth::user()->email);
            }
            $query->where('maintenance_requests.archived', 0);

            $maintenanceCounts = array();
            $applicationCounts = array();
            foreach($units as $unit){
//                applicationcounter
                $query2 = clone $query;
                $query2->where('unit_id', $unit->id);
                $maintenanceCount = $query2->count();
                $maintenanceCounts += [''.$unit->id.'' => $maintenanceCount];
                $maintenanceCountss = array_sum($maintenanceCounts);
//                applicationcounter
//                maintenanceCounter
                $unitID = ['unit_id'=>$unit->id];
                $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
                $applicationsCount = $applicationsData['applications']->count();
                $applicationCounts += [''.$unit->id.'' => $applicationsCount];
                $applicationCountss = array_sum($applicationCounts);
//                maintenanceCounter
            }
            $allMaintenanceCounts += [''.$property->id.'' => $maintenanceCountss];
            $allApplicationCountss += [''.$property->id.'' => $applicationCountss];
        }
        if (\Request::has('archived')) {
            return view(
                'properties.archive',
                [
                    'propertiesArchive' => $propertiesArchive,
                    'properties' => $properties,
                    'page' => $page,
                ]
            );
        } else {
            return view(
                'properties.index',
                [
                    'allApplicationCountss' => $allApplicationCountss,
                    'allMaintenanceCounts' => $allMaintenanceCounts,
                    'propertiesArchive' => $propertiesArchive,
                    'properties' => $properties,
                    'page' => $page,
                    'types' => PropertyType::all(),
                ]
            );
        }

    }

    public function add() {
        return view(
            'properties.add',
            [
                'states' => State::all(),
                'types' => PropertyType::all(),
            ]
        );
    }

    public function addSave(AddPropertyRequest $request)
    {
        if (Auth::user()->availableUnitsCount() < count($request->get('units', []))) {
            $maxCount = Auth::user()->activePlan() ? Auth::user()->activePlan()->subscriptionPlan->max_units : freeTrialParams('max_units');
            $upgradeLink = route('profile/membership');
            return back()->withErrors(['upgradeError'=>'According to your membership, you can create up to '.$maxCount.' units. In order to create additional unit(s), please <a href="'.$upgradeLink.'" target="_blank">upgrade your plan</a>.'])->withInput();
        }

        $prop = new Property();
        $prop->property_type_id = $request->get('type', 1);
        $prop->address = ucwords(strtolower($request->get('address', '')));
        $prop->city = ucwords(strtolower($request->get('city', '')));
        $prop->state_id = $request->get('state', 1);
        $prop->zip = $request->get('zip', '');
        $prop->purchased = $request->get('date') ? new Carbon($request->get('date')) : null;
        $prop->purchased_amount = (float) str_replace(",","",$request->get('purchased_amount', ''));
        $prop->value = (float) str_replace(",","",$request->get('current_amount', ''));
        $prop->user_id = Auth::user()->id;

        $prop->save();
        if ($request->has('property_photo')) {
            $image = new File();
            [, , , $name] = preg_split('/\//', $request->file('property_photo')->store('public/property/' . $prop->id));
            $image->filename = $name;
            $image->save();

            $prop->img = $image->id;
        }
        $prop->save();

        if ($request->has('property_gallery')) {
            foreach ($request->file('property_gallery', []) as $file) {
                $image = new File();
                [, $name] = preg_split('/\//', $file->store('public'));
                $image->filename = $name;
                $image->save();

                DB::table('image_gallery')->insert([
                    'property_id' => $prop->id,
                    'file_id' => $image->id,
                ]);
            }
        }

        foreach ($request->get('units', []) as $data) {
            $unit = new Unit();
            $unit->name = ucfirst(strtolower($data['name'])) ?? '';
            $unit->square = $data['square'] ?? null;
            $unit->bedrooms = $data['bedrooms'] ?? '';
            $unit->full_bathrooms = $data['full_bathrooms'] ?? '';
            $unit->half_bathrooms = $data['half_bathrooms'] ?? '';
            $unit->internal_notes = $data['internal_notes'] ?? '';
            $unit->description = '';
            $unit->property_id = $prop->id;
            $unit->save();

            $unit_id = empty($unit_id) ? $unit->id : $unit_id;
        }

        return redirect()->route('properties/edit', ['id' => $prop->id])
            ->with('success','Property successfully created.')
            ->with('whatsnext','It’s time to move in your tenant! Please watch this little tutorial which will show you how to do that.')
            ->with('gif', url('/').'/images/help/unit-created-movein-tenant.gif')
            ->with('whatsnext_button_text', 'Move-in tenant')
            ->with('whatsnext_button_url', route('leases/add',['unit' => $unit_id]));
    }

    public function edit(Request $request, $id) {
        $property = Auth::user()->properties->where('id', $id)->first();

        if (!$property) {
            abort(404);
        }

        if (\Request::has('archived_units')) {
            $units = $property->units->where('archived',1)->sortBy('id');
        } else {
            $units = $property->units->where('archived',0)->sortBy('id');
        }
        $query = MaintenanceRequest::query();

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('properties.user_id', Auth::id());
        } else {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('leases', 'leases.unit_id', '=', 'units.id');
            $query->where('leases.email', Auth::user()->email);
        }
        $query->where('maintenance_requests.archived', 0);

        $applicationCounts = array();
        $maintenanceCounts = array();
        foreach($units as $unit){
//      application counter
            $unitID = ['unit_id'=>$unit->id];
            $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
            $applicationsCount = $applicationsData['applications']->count();
            $applicationCounts += [''.$unit->id.'' => $applicationsCount];
//      application counter
//      maintenance counter
            $query2 = clone $query;
            $query2->where('unit_id', $unit->id);
            $maintenanceCount = $query2->count();
            $maintenanceCounts += [''.$unit->id.'' => $maintenanceCount];
//      maintenance counter
        }
//        var_dump($maintenanceCounts);
//        die;
        if (\Request::has('status')) {
            $units = $property->units->sortBy('id')
                ->filter(function($item) {
                    return $item->status == \Request::get('status');
                });
        }

        return view(
            'properties.edit',
            [
//                $applicationCounts,
                'maintenanceCounts' => $maintenanceCounts,
                'applicationCounts' => $applicationCounts,
                'property' => $property,
                'units' => $units,
                'states' => State::all(),
                'types' => PropertyType::all(),
                'id' => $id,
                'showArchiveButton' => $property->units->where('archived',1)->count() > 0,
            ]
        );
    }
    public function indexeditSave(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }
        $rules = [
            'units.*.name' => 'required|string|min:1|max:255',
            'units.*.square' => 'nullable|string|max:9',
            'units.*.bedrooms' => 'required|string|min:1',
            'units.*.full_bathrooms' => 'required|string|min:0',
            'units.*.half_bathrooms' => 'required|string|min:0',
            'units.*.description' => 'max:65000',
            //'property_photo' => 'file|nullable',
            //'property_gallery.*' => 'file|nullable',
        ];
        $attributes =  [
            'units.*.name' => 'unit name',
            'units.*.square' => 'unit square',
            'units.*.bedrooms' => 'unit bedrooms',
            'units.*.full_bathrooms' => 'unit full bathrooms',
            'units.*.half_bathrooms' => 'unit half bathrooms',
            //'property_gallery.*' => 'property gallery',
        ];
        $request->validate($rules, [], $attributes);

        $prop = Auth::user()->properties->where('id', $id)->first();

        if (!$prop) {
            abort(404);
        }

        if (Auth::user()->availableUnitsCount() < count($request->get('units', []))) {
            $maxCount = Auth::user()->activePlan() ? Auth::user()->activePlan()->subscriptionPlan->max_units : freeTrialParams('max_units');
            $upgradeLink = route('profile/membership');
            return back()->withErrors(['upgradeError'=>'According to your membership, you can create up to '.$maxCount.' units. In order to create additional unit(s), please <a href="'.$upgradeLink.'" target="_blank">upgrade your plan</a>.'])->withInput();
        }

        //$prop->property_type_id = $request->get('type', 1);
        //$prop->address = ucwords(strtolower($request->get('address', '')));
        //$prop->city = ucwords(strtolower($request->get('city', '')));
        //$prop->state_id = $request->get('state', 1);
        //$prop->zip = $request->get('zip', '');
        //$prop->purchased = $request->get('date') ? new Carbon($request->get('date')) : null;
        //$prop->purchased_amount = (float) str_replace(",","",$request->get('purchased_amount', ''));
        //$prop->value = (float) str_replace(",","",$request->get('current_amount', ''));
        //$prop->save();

        foreach ($request->get('units', []) as $data) {
            $unit = new Unit();
            $unit->name = ucfirst(strtolower($data['name'])) ?? '';
            $unit->square = $data['square'] ?? null;
            $unit->bedrooms = $data['bedrooms'] ?? '';
            $unit->full_bathrooms = $data['full_bathrooms'] ?? '';
            $unit->half_bathrooms = $data['half_bathrooms'] ?? '';
            $unit->internal_notes = $data['internal_notes'] ?? '';
            $unit->description = '';
            $unit->property_id = $prop->id;
            $unit->save();
        }

        return redirect()->route('properties')->with('success','Property successfully updated.');
    }
    public function indexedit(Request $request, $id) {
        $property = Auth::user()->properties->where('id', $id)->first();

        if (!$property) {
            abort(404);
        }

        if (\Request::has('archived_units')) {
            $units = $property->units->where('archived',1)->sortBy('id');
        } else {
            $units = $property->units->where('archived',0)->sortBy('id');
        }
        if (\Request::has('status')) {
            $units = $property->units->sortBy('id')
                ->filter(function($item) {
                    return $item->status == \Request::get('status');
                });
        }

        return view(
            'properties.index',
            [
                'property' => $property,
                'units' => $units,
                'states' => State::all(),
                'types' => PropertyType::all(),
                'id' => $id,
                'showArchiveButton' => $property->units->where('archived',1)->count() > 0,
            ]
        );
    }
    public function indexeditDetailsSave(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }
        $rules = [
            'type' => 'required|integer|min:1',
            'address' => 'required|string|min:1|max:255',
            'city' => 'required|string|min:1|max:255',
            'state' => 'required|integer|min:1',
            'zip' => 'required|string|max:5',
            //'date' => 'nullable|string|min:1',
            //'purchased_amount' => 'nullable|string|max:14',
            //'current_amount' => 'nullable|string|max:14',
        ];
        $request->validate($rules);

        $prop = Auth::user()->properties->where('id', $id)->first();

        if (!$prop) {
            abort(404);
        }

        $prop->property_type_id = $request->get('type', 1);
        $prop->address = ucwords(strtolower($request->get('address', '')));
        $prop->city = ucwords(strtolower($request->get('city', '')));
        $prop->state_id = $request->get('state', 1);
        $prop->zip = $request->get('zip', '');
        //$prop->purchased = $request->get('date') ? new Carbon($request->get('date')) : null;
        //$prop->purchased_amount = (float) str_replace(",","",$request->get('purchased_amount', ''));
        //$prop->value = (float) str_replace(",","",$request->get('current_amount', ''));
        $prop->save();

        return redirect()->route('properties', ['id' => $id])->with('success','Property successfully updated.');
    }
    public function duplicateUnit(Request $request) {
        $countCreate = $request->copy;
        $getUnitData = Unit::findOrFail($request->unit);

        if (Auth::user()->availableUnitsCount() < $countCreate) {
            $maxCount = Auth::user()->activePlan() ? Auth::user()->activePlan()->subscriptionPlan->max_units : freeTrialParams('max_units');
            $upgradeLink = route('profile/membership');
            return back()->withErrors(['upgradeError'=>'According to your membership, you can create up to '.$maxCount.' units. In order to create additional unit(s), please <a href="'.$upgradeLink.'" target="_blank">upgrade your plan</a>.'])->withInput();
        }

        //find last number in the name
        //-----------
        $name = $getUnitData->name;
        $digitsEnds = strlen($name);
        while ( $digitsEnds >= 0 ) :
            $digitsEnds--;
            if ( is_numeric( substr( $name, $digitsEnds, 1 ) ) ) :
                break;
            endif;
        endwhile;
        $digitsStarts = $digitsEnds;
        while ( $digitsStarts >= 0 ) :
            $digitsStarts--;
            if ( !is_numeric( substr( $name, $digitsStarts, 1 ) ) ) :
                break;
            endif;
        endwhile;
        if($digitsEnds < 0){
            $namePart1 = $name . " Copy ";
            $namePart2 = "0";
            $namePart3 = "";
        } else {
            $namePart1 = $digitsStarts < 0 ? "" : substr($name, 0, $digitsStarts + 1);
            $namePart2 = substr( $name, $digitsStarts + 1, $digitsEnds - $digitsStarts );
            $namePart3 = ($digitsEnds + 1) == strlen($name) ? "" : substr($name, $digitsEnds + 1, strlen($name) - $digitsEnds - 1);

            $zerosEnds = 0;
            while ( $zerosEnds < strlen($namePart2) ) :
                if ( substr( $namePart2, $zerosEnds, 1 ) === "0" ){
                    $zerosEnds++;
                } else {
                    break;
                }
            endwhile;
            if($zerosEnds > 0){
                $namePart1 = $namePart1 . str_pad( "", $zerosEnds, "0" );
                $namePart2 = substr( $namePart2, $zerosEnds);
            }
        }
        $incrementPart = (int)$namePart2;
        //-----------

        for ($i = 1; $i <= $countCreate; $i++) {
            $unit = $getUnitData->replicate();
            $incrementPart++;
            $unit->name = $namePart1 . $incrementPart . $namePart3;
            $unit->save();
            foreach ($getUnitData->amenities as $unitAmenitie) {
                $amenitie = $unitAmenitie->replicate();
                $amenitie->unit_id = $unit->id;
                $amenitie->save();
            }
        }

        return redirect()->route('properties/edit', ['id' => $getUnitData->property_id])->with('success','Unit "' . $name . '" successfully duplicated.');
    }

    public function editSave(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }
        $rules = [
            'units.*.name' => 'required|string|min:1|max:255',
            'units.*.square' => 'nullable|string|max:9',
            'units.*.bedrooms' => 'required|string|min:1',
            'units.*.full_bathrooms' => 'required|string|min:0',
            'units.*.half_bathrooms' => 'required|string|min:0',
            'units.*.description' => 'max:65000',
            //'property_photo' => 'file|nullable',
            //'property_gallery.*' => 'file|nullable',
        ];
        $attributes =  [
            'units.*.name' => 'unit name',
            'units.*.square' => 'unit square',
            'units.*.bedrooms' => 'unit bedrooms',
            'units.*.full_bathrooms' => 'unit full bathrooms',
            'units.*.half_bathrooms' => 'unit half bathrooms',
            //'property_gallery.*' => 'property gallery',
        ];
        $request->validate($rules, [], $attributes);

        $prop = Auth::user()->properties->where('id', $id)->first();

        if (!$prop) {
            abort(404);
        }

        if (Auth::user()->availableUnitsCount() < count($request->get('units', []))) {
            $maxCount = Auth::user()->activePlan() ? Auth::user()->activePlan()->subscriptionPlan->max_units : freeTrialParams('max_units');
            $upgradeLink = route('profile/membership');
            return back()->withErrors(['upgradeError'=>'According to your membership, you can create up to '.$maxCount.' units. In order to create additional unit(s), please <a href="'.$upgradeLink.'" target="_blank">upgrade your plan</a>.'])->withInput();
        }

        //$prop->property_type_id = $request->get('type', 1);
        //$prop->address = ucwords(strtolower($request->get('address', '')));
        //$prop->city = ucwords(strtolower($request->get('city', '')));
        //$prop->state_id = $request->get('state', 1);
        //$prop->zip = $request->get('zip', '');
        //$prop->purchased = $request->get('date') ? new Carbon($request->get('date')) : null;
        //$prop->purchased_amount = (float) str_replace(",","",$request->get('purchased_amount', ''));
        //$prop->value = (float) str_replace(",","",$request->get('current_amount', ''));
        //$prop->save();

        foreach ($request->get('units', []) as $data) {
            $unit = new Unit();
            $unit->name = ucfirst(strtolower($data['name'])) ?? '';
            $unit->square = $data['square'] ?? null;
            $unit->bedrooms = $data['bedrooms'] ?? '';
            $unit->full_bathrooms = $data['full_bathrooms'] ?? '';
            $unit->half_bathrooms = $data['half_bathrooms'] ?? '';
            $unit->internal_notes = $data['internal_notes'] ?? '';
            $unit->description = '';
            $unit->property_id = $prop->id;
            $unit->save();
        }

        return redirect()->route('properties/edit', ['id' => $id])->with('success','Property successfully updated.');
    }

    public function editDetailsSave(Request $request, $id) {
        if ($id === null) {
            abort(404);
        }
        $rules = [
            'type' => 'required|integer|min:1',
            'address' => 'required|string|min:1|max:255',
            'city' => 'required|string|min:1|max:255',
            'state' => 'required|integer|min:1',
            'zip' => 'required|string|max:5',
            //'date' => 'nullable|string|min:1',
            //'purchased_amount' => 'nullable|string|max:14',
            //'current_amount' => 'nullable|string|max:14',
        ];
        $request->validate($rules);

        $prop = Auth::user()->properties->where('id', $id)->first();

        if (!$prop) {
            abort(404);
        }

        $prop->property_type_id = $request->get('type', 1);
        $prop->address = ucwords(strtolower($request->get('address', '')));
        $prop->city = ucwords(strtolower($request->get('city', '')));
        $prop->state_id = $request->get('state', 1);
        $prop->zip = $request->get('zip', '');
        //$prop->purchased = $request->get('date') ? new Carbon($request->get('date')) : null;
        //$prop->purchased_amount = (float) str_replace(",","",$request->get('purchased_amount', ''));
        //$prop->value = (float) str_replace(",","",$request->get('current_amount', ''));
        $prop->save();

        return redirect()->route('properties/edit', ['id' => $id])->with('success','Property successfully updated.');
    }

    public function operations($id)
    {
        $property = Auth::user()->properties->where('id', $id)->first();

        if (!$property) {
            abort(404);
        }

        return view(
            'properties.operations',
            [
                'property' => $property,
                'states' => State::all(),
                'types' => PropertyType::all(),
                'id' => $id,
            ]
        );
    }

    public function operationsSave(Request $request, $id)
    {
        $property = Auth::user()->properties->where('id', $id)->first();

        if (!$property) {
            abort(404);
        }

        if($request->archive == 1){
            //check for active leases
            $query = DB::table('leases');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->where('units.property_id', '=', $property->id);
            $query->whereNull('leases.deleted_at');
            if($query->count() > 0){
                return redirect()->route('properties/operations', ['property' => $property->id])->with('error','This Property can\'t be archived because it has an active Lease.');
            }

            // archive property, units, applications and maintenance.
            // leases, bills, invoices and payments will not be visible then

            DB::statement( 'UPDATE `maintenance_requests`, `units` SET `maintenance_requests`.`archived` = 1 WHERE `units`.`id` = `maintenance_requests`.`unit_id` AND `units`.`property_id` =:property_id', ['property_id' => $property->id] );
            DB::statement( 'UPDATE `applications`, `units` SET `applications`.`archived` = 1 WHERE `units`.`id` = `applications`.`unit_id` AND `units`.`property_id` =:property_id', ['property_id' => $property->id] );
            //DB::statement( 'UPDATE `units` SET `archived` = 1 WHERE `property_id` =:property_id', ['property_id' => $property->id] );
            DB::statement( 'UPDATE `properties` SET `archived` = 1 WHERE `id` =:property_id', ['property_id' => $property->id] );

            return redirect()->route('properties')->with('success','Property successfully archived.');
        }

        if($request->delete == 1){
            //check for active leases
            $query = DB::table('leases');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->where('units.property_id', '=', $property->id);
            $query->whereNull('leases.deleted_at');
            if($query->count() > 0){
                return redirect()->route('properties/operations', ['property' => $property->id])->with('error','This Property can\'t be deleted because it has an active Lease.');
            }

            Property::deleteProperty($property->id);

            return redirect()->route('properties')->with('success','Property successfully deleted.');
        }
    }

    public function unarchive(Request $request) {
        $property = Auth::user()->properties->where('id', $request->get('record_id'))->first();
        if (!$property) {
            abort(404);
        }

        DB::statement( 'UPDATE `applications`, `units` SET `applications`.`archived` = 0 WHERE `units`.`id` = `applications`.`unit_id` AND `units`.`property_id` =:property_id', ['property_id' => $property->id] );
        //DB::statement( 'UPDATE `units` SET `archived` = 0 WHERE `property_id` =:property_id', ['property_id' => $property->id] );
        DB::statement( 'UPDATE `properties` SET `archived` = 0 WHERE `id` =:property_id', ['property_id' => $property->id] );

        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

}
