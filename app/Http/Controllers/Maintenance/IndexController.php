<?php

namespace App\Http\Controllers\Maintenance;

use App\Models\MaintenanceDocument;
use App\Models\ServicePro;
use App\Notifications\LandlordInviteTenant;
use App\Notifications\MaintenanceRequestToAssignServiceProfessional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\MaintenanceRequestStatus;
use App\Models\MaintenanceRequestPriority;
use App\Models\MaintenanceRequestMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\TenantCreateMaintenanceRequest;
use App\Notifications\TenantChangeMaintenanceRequestStatus;
use App\Notifications\LandlordCreateMaintenanceRequest;
use App\Notifications\LandlordChangeMaintenanceRequestStatus;
use App\Models\Lease;
use App\Models\Property;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;
//use Illuminate\Support\Facades\Notification;

class IndexController extends Controller
{
    public function dashboard(Request $request) {
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
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $queryarchive = clone $query;
            $queryarchive->where('maintenance_requests.archived', 1);
            $queryarchivecount = $queryarchive->count();

        } else {
            $queryarchivecount = 0;
        }

        $query->where('maintenance_requests.archived', 0);

        $countWithoutFilter = $query->count();

        if (!empty($request['status_id'])) {
            $query->where('maintenance_requests.status_id', $request['status_id']);
        }
        if (!empty($request['priority_id'])) {
            $query->where('maintenance_requests.priority_id', $request['priority_id']);
        }
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query->where('maintenance_requests.unit_id', $property_id_unit_id[1]);
            } else {
                $query->where('units.property_id', $property_id_unit_id[0]);
            }
        }

        $query2 = clone $query;
        $query3 = clone $query;
        $maintenanceRequestsNew = $query->where('status_id',1)->select(['maintenance_requests.*'])->get();
        $maintenanceRequestsNew2=Array();
        foreach ($maintenanceRequestsNew as $main){
            $qq = DB::table('leases')->where('unit_id', '=', $main->unit_id)->orderBy('id', 'desc')->first();
            $qq2 = DB::table('units')->where('id', '=', $main->unit_id)->first();
            $qq3 = DB::table('properties')->where('id', '=', $qq2->property_id)->first();
            $qq4 = DB::table('services_pro')->where('id', '=', $main->service_pro)->first();
            $main->deleted_at=$qq->deleted_at;
            $main->property_address=$qq3->address;
            if(isset($qq4->display_as_company)){
                if($qq4->display_as_company >0){
                    $main->service_pro_company_name=$qq4->company_name.'(company)';
                    $main->service_pro_company_website=$qq4->company_website;

                }else{
                    $main->service_pro_first_name=$qq4->first_name;
                    $main->service_pro_last_name=$qq4->last_name;
                    $main->service_pro_middle_name=$qq4->middle_name;
                }
            }else{

            }
            array_push($maintenanceRequestsNew2,$main);
        }

        $maintenanceRequestsInProgress = $query2->where('status_id',2)->select(['maintenance_requests.*'])->get();
        $maintenanceRequestsInProgress2=Array();
        foreach ($maintenanceRequestsInProgress as $main){
            $qq = DB::table('leases')->where('unit_id', '=', $main->unit_id)->orderBy('id', 'desc')->first();
            $qq2 = DB::table('units')->where('id', '=', $main->unit_id)->first();
            $qq3 = DB::table('properties')->where('id', '=', $qq2->property_id)->first();
            $qq4 = DB::table('services_pro')->where('id', '=', $main->service_pro)->first();
            $main->deleted_at=$qq->deleted_at;
            $main->property_address=$qq3->address;
            if(isset($qq4->display_as_company)){
                if($qq4->display_as_company >0){
                    $main->service_pro_company_name=$qq4->company_name.'(company)';
                    $main->service_pro_company_website=$qq4->company_website;

                }else{
                    $main->service_pro_first_name=$qq4->first_name;
                    $main->service_pro_last_name=$qq4->last_name;
                    $main->service_pro_middle_name=$qq4->middle_name;
                }
            }else{

            }
            array_push($maintenanceRequestsInProgress2,$main);
        }

        $maintenanceRequestsResolved = $query3->where('status_id',3)->select(['maintenance_requests.*'])->get();
        $maintenanceRequestsResolved2=Array();
        foreach ($maintenanceRequestsResolved as $main){
            $qq = DB::table('leases')->where('unit_id', '=', $main->unit_id)->orderBy('id', 'desc')->first();
            $qq2 = DB::table('units')->where('id', '=', $main->unit_id)->first();
            $qq3 = DB::table('properties')->where('id', '=', $qq2->property_id)->first();
            $qq4 = DB::table('services_pro')->where('id', '=', $main->service_pro)->first();
            $main->deleted_at=$qq->deleted_at;
            $main->property_address=$qq3->address;
            if(isset($qq4->display_as_company)){
                if($qq4->display_as_company >0){
                    $main->service_pro_company_name=$qq4->company_name.'(company)';
                    $main->service_pro_company_website=$qq4->company_website;

                }else{
                    $main->service_pro_first_name=$qq4->first_name;
                    $main->service_pro_last_name=$qq4->last_name;
                    $main->service_pro_middle_name=$qq4->middle_name;
                }
            }else{

            }
            array_push($maintenanceRequestsResolved2,$main);
        }
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $properties_query = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, 0 AS unit_id, "" AS unit_name'));
            $properties_units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->union($properties_query)
                ->orderBy('property_id', 'asc')
                ->orderBy('unit_id', 'asc')
                ->get();

            $units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->orderBy('properties.id', 'asc')
                ->orderBy('units.id', 'asc')
                ->select(['units.*', 'units.id AS unit_id'])
                ->get();
        } else {
            $properties_units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->join('properties', 'properties.id', '=', 'units.property_id')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->get();

            $units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->whereNull('leases.deleted_at')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT units.*'))
                ->get();
        }

        $statuses = MaintenanceRequestStatus::all();
        $priorities = MaintenanceRequestPriority::all();

        $draftDocuments = [];
        foreach ($statuses as $status) {
            if ($status->name === 'Draft') {
                $draft = MaintenanceRequest::where("creator_user_id", Auth::user()->id)
                    ->where("status_id", $status->id)
                    ->first();
                if(!empty($draft)){
                    $draftDocuments = $draft->maintenanceDocuments()->get();
                }
            }
        }

        $servicePross = ServicePro::all(['id','user_id','first_name', 'last_name', 'middle_name', 'company_name', 'company_website', 'email', 'phone', 'fax', 'tax_identity_type', 'tax_payer_id', 'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip', 'country']);
        $servicePros = $servicePross->where('user_id','=',Auth::user()->id);
        $exCategory = DB::table('expense_types')
            ->where('pid', '=', null)
            ->get();
        $exSubCategory = DB::table('expense_types')
            ->where('pid', '<>', null)
            ->get();
        $allExCategory = Array();
        $allExSubCategory = Array();
        foreach($exCategory as  $value){
            array_push($allExCategory, $value);
        }
        foreach($exSubCategory as  $value){
            array_push($allExSubCategory, $value);
        }

        return view('maintenance.dashboard', [
            'allcategory' => $allExCategory,
            'allsubcategory' => $allExSubCategory,
            'servicePros' => $servicePros,
            'maintenanceRequestsNew' => $maintenanceRequestsNew2,
//            'maintenanceRequestsNew' => $maintenanceRequestsNew,
            'maintenanceRequestsInProgress' => $maintenanceRequestsInProgress2,
            'maintenanceRequestsResolved' => $maintenanceRequestsResolved2,
            'units' => $units,
            'properties_units' => $properties_units,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'countWithoutFilter' => $countWithoutFilter,
            'draft' => $draft ?? null,
            'draftDocuments' => $draftDocuments,
            'queryArchiveCount' => $queryarchivecount
        ]);
    }

    public function listView(Request $request) {
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

        if (!empty($request['archived'])) {
            $query->where('maintenance_requests.archived', 1);
        } else {
            $query->where('maintenance_requests.archived', 0);
        }

        $countWithoutFilter = $query->count();

        if (!empty($request['status_id'])) {
            $query->where('maintenance_requests.status_id', $request['status_id']);
        }
        if (!empty($request['priority_id'])) {
            $query->where('maintenance_requests.priority_id', $request['priority_id']);
        }
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query->where('maintenance_requests.unit_id', $property_id_unit_id[1]);
            } else {
                $query->where('units.property_id', $property_id_unit_id[0]);
            }
        }

        //Sorting
        $availableColumns = [
            'maintenance_requests.id',
            'maintenance_requests.priority_id',
            'maintenance_requests.created_at',
            'properties.address',
            'maintenance_requests.name',
            'maintenance_requests.status_id'
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        }

        $maintenanceRequests = $query->select(['maintenance_requests.*'])->paginate(50);
        $maintenanceRequests2=Array();
        foreach ($maintenanceRequests as $main){
            $qq = DB::table('leases')->where('unit_id', '=', $main->unit_id)->orderBy('id', 'desc')->first();
            $qq2 = DB::table('units')->where('id', '=', $main->unit_id)->first();
            $qq3 = DB::table('properties')->where('id', '=', $qq2->property_id)->first();
            $qq4 = DB::table('services_pro')->where('id', '=', $main->service_pro)->first();
            $main->deleted_at=$qq->deleted_at;
            $main->property_address=$qq3->address;
            if(isset($qq4->display_as_company)){
                if($qq4->display_as_company >0){
                    $main->service_pro_company_name=$qq4->company_name.'(company)';
                    $main->service_pro_company_website=$qq4->company_website;

                }else{
                    $main->service_pro_first_name=$qq4->first_name;
                    $main->service_pro_last_name=$qq4->last_name;
                    $main->service_pro_middle_name=$qq4->middle_name;
                }
            }else{

            }

            array_push($maintenanceRequests2,$main);
        }
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $properties_query = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, 0 AS unit_id, "" AS unit_name'));
            $properties_units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->union($properties_query)
                ->orderBy('property_id', 'asc')
                ->orderBy('unit_id', 'asc')
                ->get();

            $units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->orderBy('properties.id', 'asc')
                ->orderBy('units.id', 'asc')
                ->select(['units.*', 'units.id AS unit_id'])
                ->get();
        } else {
            $properties_units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->join('properties', 'properties.id', '=', 'units.property_id')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->get();

            $units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->whereNull('leases.deleted_at')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT units.*'))
                ->get();
        }

        $statuses = MaintenanceRequestStatus::all();
        $priorities = MaintenanceRequestPriority::all();

        $draftDocuments = [];
        foreach ($statuses as $status) {
            if ($status->name === 'Draft') {
                $draft = MaintenanceRequest::where("creator_user_id", Auth::user()->id)
                    ->where("status_id", $status->id)
                    ->first();
                if(!empty($draft)){
                    $draftDocuments = $draft->maintenanceDocuments()->get();
                }
            }
        }


        $servicePros = ServicePro::all(['id','first_name', 'last_name', 'middle_name', 'company_name', 'company_website', 'email', 'phone', 'fax', 'tax_identity_type', 'tax_payer_id', 'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip', 'country']);
        $servicePros->where('user_id','=',Auth::user()->id);
        $exCategory = DB::table('expense_types')
            ->where('pid', '=', null)
            ->get();
        $exSubCategory = DB::table('expense_types')
            ->where('pid', '<>', null)
            ->get();
        $allExCategory = Array();
        $allExSubCategory = Array();
        foreach($exCategory as  $value){
            array_push($allExCategory, $value);
        }
        foreach($exSubCategory as  $value){
            array_push($allExSubCategory, $value);
        }

        return view('maintenance.list-view',[
            'allcategory' => $allExCategory,
            'allsubcategory' => $allExSubCategory,
            'servicePros' => $servicePros,
            'maintenanceRequests' => $maintenanceRequests2,
            'units' => $units,
            'properties_units' => $properties_units,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'countWithoutFilter' => $countWithoutFilter,
            'draft' => $draft ?? null,
            'draftDocuments' => $draftDocuments,
        ]);
    }

    public function viewDetails(Request $request) {
        $maintenanceRequest = MaintenanceRequest::find($request->get('record_id'));
        $title = "#" . $maintenanceRequest->id . ", "
            . Carbon::parse($maintenanceRequest->created_at)->format('m/d/Y');

        $list = [];

        foreach ($maintenanceRequest->messages as $message) {
            $list[] = [
                Carbon::parse($message->created_at)->format('m/d/y, h:mA'),
                $message->creator->name . ' ' . $message->creator->lastname,
                $message->text,
            ];
        }

        $documents = [];
        foreach ($maintenanceRequest->maintenanceDocuments as $document) {
            $documents[] = [
                'name' => $document->name,
                'url' => "/storage/" . $document->filepath,
                'icon' => $document->icon(),
                'id' => $document->id,
                'can_delete' => ($document->user_id == Auth::user()->id) ? 1 : 0
            ];
        }

        $query = User::query();
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query->join('leases', 'leases.email', '=', 'users.email')
                ->join('units', 'units.id', '=', 'leases.unit_id')
                ->join('maintenance_requests', 'maintenance_requests.unit_id', '=', 'units.id');
        } else {
            $query->join('properties', 'properties.user_id', '=', 'users.id')
                ->join('units', 'units.property_id', '=', 'properties.id')
                ->join('maintenance_requests', 'maintenance_requests.unit_id', '=', 'units.id');
        }
        $query->where('maintenance_requests.id', $request->get('record_id'));
        $contactPerson = $query->select(['users.*'])->first();
        $contact = "";
        if($contactPerson) {
            $contact = $contactPerson->fullName() . " ( " . $contactPerson->email . ($contactPerson->phone ? ", " . $contactPerson->phone : "") . " )";
        }

        $result = [
            "result" => "success",
            "name" => $maintenanceRequest->name,
            "description" => $maintenanceRequest->description,
            "title" => $title,
            "id" => $title,
            "list" => $list,
            "documents" => $documents,
            "contact" => $contact
        ];

        return json_encode($result);
    }

    public function add(Request $request) {
        $statuses = MaintenanceRequestStatus::all();

        if(!empty($request->maintenance_request_id)){
            $maintenanceRequest = MaintenanceRequest::where("id", $request->maintenance_request_id)
            ->where("creator_user_id", Auth::user()->id)->first();
        }
        if(empty($maintenanceRequest)){
            $maintenanceRequest = new MaintenanceRequest();
        }

        $maintenanceRequest->name = $request->get('name');
        $maintenanceRequest->description = $request->get('description');
        $maintenanceRequest->creator_user_id = Auth::user()->id;
        foreach ($statuses as $status) {
            if ($status->name === 'New') {
                $maintenanceRequest->status_id = $status->id;
            }
        }
        $maintenanceRequest->priority_id = $request->get('priority_id');
        $maintenanceRequest->unit_id = $request->get('unit_id');
        $maintenanceRequest->service_pro = $request->get('service_pro');
        $maintenanceRequest->archived = 0;
        $maintenanceRequest->save();

        $servicePros = ServicePro::all(['id','first_name', 'last_name', 'middle_name', 'company_name', 'company_website', 'email', 'phone', 'fax', 'tax_identity_type', 'tax_payer_id', 'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip', 'country']);
        $servicePro = $servicePros->where('id','=',$maintenanceRequest->service_pro);
        if (isset($servicePro)){
            $email = $servicePro->email;
            $landlord = Auth::user();
//            $unitObj = DB::table('units')->where('id', '=',$request->get('property'))->first();
//            Notification::route('mail', $email)->notify(new MaintenanceRequestToAssignServiceProfessional($landlord, $maintenanceRequest));
            $landlord->notify(new MaintenanceRequestToAssignServiceProfessional($landlord, $maintenanceRequest));
        }

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //send notification to tenant (if exists)
            $unit = $maintenanceRequest->unit;
            if(count($unit->leases) > 0) {
                foreach ($unit->leases as $lease) {
                    if (empty($lease->deleted_at)) {
                        $tenant = User::where('email', $lease->email)->first();
                        $tenant->notify(new LandlordCreateMaintenanceRequest($tenant, $maintenanceRequest));
                        break;
                    }
                }
            }
        } else {
            //send notification to landlord
            $landlord = $maintenanceRequest->unit->property->user;
            $landlord->notify(new TenantCreateMaintenanceRequest($landlord, $maintenanceRequest));
        }

        $result = view('maintenance.maintenance_request', [
            'maintenanceRequest' => $maintenanceRequest
        ]);

        return $result;
    }

    public function addMessage(Request $request) {
        $message = new MaintenanceRequestMessage();
        $message->text = $request->get('text');
        $message->maintenance_request_id = $request->get('id');
        $message->creator_user_id = Auth::user()->id;

        $message->save();

        $formattedMessage = [
            Carbon::parse($message->created_at)->format('m/d/Y h:m a'),
            $message->creator->name . ' ' . $message->creator->lastname,
            $message->text,
        ];

        $result = [
            "result" => "success",
            "message" => $formattedMessage
        ];

        return json_encode($result);
    }

    public function delete(Request $request) {
        $maintenanceRequest = MaintenanceRequest::find($request->get('record_id'));
        foreach ($maintenanceRequest->messages as $message) {
            $message->delete();
        }
        foreach($maintenanceRequest->maintenanceDocuments as $document){
            Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();
        }
        $maintenanceRequest->delete();
        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

    public function archive(Request $request) {
        $maintenanceRequest = MaintenanceRequest::find($request->get('record_id'));
        $maintenanceRequest->archived = 1;
        $maintenanceRequest->save();
        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

    public function unarchive(Request $request) {
        $maintenanceRequest = MaintenanceRequest::find($request->get('record_id'));
        $maintenanceRequest->archived = 0;
        $maintenanceRequest->save();
        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

    public function autocomplete(Request $request) {
        $property = Property::where('address', 'like', '%' . $request->q . '%')->get();
        $unit = Unit::where('name', 'like', '%' . $request->q . '%')->get();
        $arr['props'] = $property;
        $arr['units'] = $unit;
        return response()->json([$property, $unit]);
    }

    public function updateStatus(Request $request) {
        $statuses = MaintenanceRequestStatus::all();

        $maintenanceRequest = MaintenanceRequest::find($request->get('id'));
        foreach ($statuses as $status) {
            if ($status->name ===  $request->get('status')) {
                $maintenanceRequest->status_id = $status->id;
            }
        }
        $maintenanceRequest->save();

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //send notification to tenant (if exists)
            $unit = $maintenanceRequest->unit;
            if(count($unit->leases) > 0) {
                foreach ($unit->leases as $lease) {
                    if (empty($lease->deleted_at)) {
                        $tenant = User::where('email', $lease->email)->first();
                        $tenant->notify(new LandlordChangeMaintenanceRequestStatus($tenant, $maintenanceRequest));
                        break;
                    }
                }
            }
        } else {
            //send notification to landlord
            $landlord = $maintenanceRequest->unit->property->user;
            $landlord->notify(new TenantChangeMaintenanceRequestStatus($landlord, $maintenanceRequest));
        }

        $maintenanceRequest = MaintenanceRequest::find($request->get('id'));
        $status = $maintenanceRequest->status->name;

        $result = [
            "result" => "success",
            "status" => $status,
            "maintenanceRequest" => $maintenanceRequest,
        ];

        return json_encode($result);
    }

    public function documentUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'csv', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mpeg'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        if(!empty($request->maintenance_request_id)){
            $maintenance_request_id = $request->maintenance_request_id;
        } else {
            $statuses = MaintenanceRequestStatus::all();
            $maintenanceRequest = new MaintenanceRequest();
            $maintenanceRequest->creator_user_id = Auth::user()->id;
            foreach ($statuses as $status) {
                if ($status->name === 'Draft') {
                    $maintenanceRequest->status_id = $status->id;
                }
            }
            $maintenanceRequest->priority_id = 1;
            $maintenanceRequest->archived = 0;
            $maintenanceRequest->save();
            $maintenance_request_id = $maintenanceRequest->id;
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $filePath = 'public/maintenance/' . $user->id . '/' . $maintenance_request_id;
                [, $filepath] = preg_split('/\//', $document_file->store($filePath), 2);

                $document = new MaintenanceDocument();
                $document->user_id = $user->id;
                $document->maintenance_request_id = $maintenance_request_id;
                $document->filepath = $filepath;
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                $target_file_path_output[] = [
                    'url' => url('storage/' . $filepath),
                    'name' => $document->name,
                    'icon' => $document->icon(),
                    'id' => $document->id,
                ];

            } else {
                $target_file_path_output[] = [
                    'error' => 'File type not allowed',
                    'name' => $document_file->getClientOriginalName(),
                    'icon' => '<i class="fal fa-file"></i>',
                    'id' => '0',
                ];
            }

        }

        $output = ['uploaded' => $target_file_path_output, 'maintenance_request_id' => $maintenance_request_id];
        return response()->json($output);
    }

    public function documentDelete(Request $request)
    {
        $user = Auth::user();
        $document = MaintenanceDocument::where(['id' => $request->document_id, 'user_id' => $user->id ])->first();

        if(!empty($document)) {
            $document_id = $document->id;

            Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();

            $output = ['success' => 'Processed', 'document_id' => $document_id];
            return response()->json($output);
        }
    }

    public function draftDelete(Request $request)
    {
        $user = Auth::user();
        $draft = MaintenanceRequest::where(['id' => $request->maintenance_request_id, 'creator_user_id' => $user->id ])->first();

        if(!empty($draft)) {
            foreach($draft->maintenanceDocuments as $document){
                Storage::delete('public/' . $document->filepath);
                if(!empty($document->thumbnailpath)){
                    Storage::delete('public/' . $document->thumbnailpath);
                }
                $document->delete();
            }
            $draft->delete();
            $output = ['success' => 'Processed'];
            return response()->json($output);
        }
    }

}
