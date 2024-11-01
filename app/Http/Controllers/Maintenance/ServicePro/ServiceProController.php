<?php

namespace App\Http\Controllers\Maintenance\ServicePro;

use App\Models\ServicePro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;

class ServiceProController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $u = $user->id;
        $servicePross = ServicePro::all(['id','user_id','first_name', 'last_name', 'middle_name', 'company_name', 'company_website', 'email', 'phone', 'fax', 'tax_identity_type', 'tax_payer_id', 'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip', 'country']);
        $servicePros = $servicePross->where('user_id','=',$u);
        $categories =DB::table('services_pro_categories')->where('user_id', $u)
            ->orWhereNull('user_id')
            ->get(['id', 'user_id', 'name']);
        $taxes =DB::table('tax_identity_types')->where('user_id', $u)
            ->orWhereNull('user_id')
            ->get(['id', 'user_id', 'name']);
        return view('maintenance.service-pro', compact('servicePros','categories','taxes'));
    }
    public function view($id)
    {
        $user = Auth::user();
        $u = $user->id;
        $servicePross = ServicePro::all(['id','user_id','first_name', 'last_name', 'middle_name', 'company_name', 'company_website', 'email', 'phone', 'fax', 'tax_identity_type', 'tax_payer_id', 'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip', 'country']);
        $servicePros = $servicePross->where('user_id','=',$u);
        $servicePros = $servicePros->where('id','=',$id);
        $categories =DB::table('services_pro_categories')->where('user_id', $u)
            ->orWhereNull('user_id')
            ->get(['id', 'user_id', 'name']);
        $taxes =DB::table('tax_identity_types')->where('user_id', $u)
            ->orWhereNull('user_id')
            ->get(['id', 'user_id', 'name']);
        return view('maintenance.view-edit', compact('servicePros','categories','taxes'));
    }

    public function delete($id)
    {
        $user = Auth::user();
        $u = $user->id;

        $service_data = DB::table('services_pro')->where('id', $id)->first();
        if ($service_data->user_id == $u){
            DB::table('services_pro')->where('id', $id)->delete();
        }else{
            return response()->json(['message' => 'Item not deleted.']);
        }
        return response()->json(['message' => 'Item deleted successfully.']);
    }

    public function store(Request $request)
    {
            $user = Auth::user();
            $u = $user->id;
            $servicePro = new ServicePro();
            $servicePro->user_id = $u;
            $servicePro->first_name = $request->first_name;
            $servicePro->last_name = $request->last_name;
            $servicePro->middle_name = $request->middle_name;
            $servicePro->company_name = $request->company_name;
            $servicePro->company_website = $request->company_website;
            $servicePro->email = $request->email;
            $servicePro->phone = $request->phone;
            $servicePro->fax = $request->fax;
            $servicePro->tax_identity_type = $request->tax_identity_type;
            $servicePro->tax_payer_id = $request->tax_payer_id;
            $servicePro->display_as_company = $request->display_as_company;
            $servicePro->category = $request->category;
            $servicePro->street_address = $request->street_address;
            $servicePro->city = $request->city;
            $servicePro->state_region = $request->state_region;
            $servicePro->zip = $request->zip;
            $servicePro->country = $request->country;
            $servicePro->save();
            return response()->json(['message' => $request]);
    }
}
