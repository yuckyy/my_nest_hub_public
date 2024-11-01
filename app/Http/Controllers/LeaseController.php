<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLeaseRequest;
use App\Http\Requests\EditLeaseRequest;
use App\Jobs\EndLease;
use App\Models\Application;
use App\Models\Role;

use App\Models\UserIdentity;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use App\Services\DwollaService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Document;
use App\Models\Bill;
use App\Models\MoveIn;
use App\Models\FinanceUnit;
use Carbon\Carbon;
use App\Rules\LeaseStartDate;
use App\Rules\LeaseEndDate;
use App\Rules\LeaseStartDateSelf;
use App\Rules\LeaseEndDateSelf;
use App\Rules\MonthToMonth;
use App\Rules\RentAssistance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Notifications\LandlordEndedLease;
use App\Notifications\TenantEndedLease;
use App\Notifications\TenantChangedLease;
use App\Notifications\TenantCreatedLease;
use Illuminate\Support\Facades\Storage;
use App\Models\Financial;
use App\Models\Invoice;
use App\Services\StripeService;
use App\Notifications\ConnectStripeAccount;
use Illuminate\Validation\Rule;
use App\Notifications\TenantWasCreated;
use Intervention\Image\ImageManagerStatic as Image;

class LeaseController extends Controller
{
    //
    private $ur;
    private $lr;

    public function __construct(UserRepositoryInterface $ur, LeasesRepositoryInterface $lr) {
        $this->ur = $ur;
        $this->lr = $lr;
    }

    public function add(Request $request) {
        $user = Auth::user();

        if(!empty($request->get('application'))) {
            //check if can view
            $shared = Application::leftJoin('applications_users', 'applications.id', '=', 'applications_users.application_id')
                ->where('applications.id', $request->get('application'))
                ->where('applications_users.user_id', $user->id)->count();
            $owner = Application::where('user_id', $user->id)
                ->where('applications.id', $request->get('application'))
                ->count();
            if (!$shared && !$owner) {
                return response()->view('errors.' . '404-app', [], 404);
            }

            DB::update('UPDATE `applications_users` set `is_new` = FALSE WHERE `application_id` = ? AND `user_id` = ?', [$request->get('application'), $user->id]);
        }

        $unit = $request['unit'] ? Unit::find($request['unit']) : null;
        if(!empty($unit) && ($unit->property->user_id != $user->id)){
            $unit = null;
        }

        $application = Application::withTrashed()->find($request->get('application'));

        $step = $request->session()->get('lease_add_step', 1);
        $data = $request->session()->get('lease', []);

        if( empty($request->get('step')) ||
            (!empty($data['application_id']) && ($step == 1) && ($request->get('application') != $data['application_id'])) ||
            (empty($data['application_id']) && ($step == 1) && (!empty($request->get('application'))))
        ){
            //initialize process
            $data = [];
            $request->session()->forget('lease');
            $request->session()->forget('lease_add_step');
            $request->session()->forget('referred_url');
            $step = 1;

            $referredUrl = URL::previous();
            if(
                (strpos($referredUrl, url('/')."/properties") === 0) ||
                (strpos($referredUrl, url('/')."/applications") === 0)
            ){
                $request->session()->put('referred_url', $referredUrl);
            }
        }

        $documents = Document::where(['user_id' => Auth::user()->id, 'document_type' => 'shared_document'])->whereNull('lease_id')->get();
        $moveInPhotos = Document::where(['user_id' => Auth::user()->id, 'document_type' => 'move_in_photo'])->whereNull('lease_id')->get();

        return view(
            "leases.add.step_$step",
            [
                'application' => $application ?? '',
                'unit' => $unit,
                'property_id' => $request['property'] ?? "",
                'step' => $step,
                'data' => $data,
                'defaultBills' => Bill::where('lease_id', null)->cursor(),
                'documents' => $documents,
                'moveInPhotos' => $moveInPhotos,
            ]
        );
    }

    public function addSave(Request $request) {
        if(!empty($request->get('cancel'))){
            $request->session()->forget('lease');
            $request->session()->forget('lease_add_step');

            if(!empty($request->session()->get('referred_url'))){
                $url = $request->session()->get('referred_url');
                $request->session()->forget('referred_url');
                return redirect($url);
            } else {
                return redirect()->route('dashboard');
            }
        }

        $step = $request->session()->get('lease_add_step', 1);
        $step = $request->get('step', $step);

        $data = $request->session()->get('lease', []);

        switch ($step) {
            case 1:

                Validator::make($request->all(), [
                    'firstname' => 'required|string|min:1|max:127',
                    'lastname' => 'required|string|min:1|max:127',
                    'email' => 'required|email|min:1|unique_tenant|max:127',
                    'phone' => 'required|string|min:1|max:20',
                    'property' => 'required|integer|min:1',
                    'unit' => 'required|integer|min:1',
                    'start_date' => ['required', new LeaseStartDate($request['unit'],$request['end_date'])],
                    'due_date' => 'required|integer|min:1|max:31',
                    'amount' => ['required','string','max:14', Rule::notIn(['0','0.00']),],
                    'month_to_month' => ['required_without:end_date', new MonthToMonth($request['unit'],$request['start_date'])],
                    'end_date' => ['required_without:month_to_month','greater_than_field:start_date', new LeaseEndDate($request['unit'],$request['start_date'])],
                ], $messages = [
                    'not_in' => "Can't be zero",
                ])->validate();

                $data['firstname'] = $request->get('firstname') ?? '';
                $data['lastname'] = $request->get('lastname') ?? '';
                $data['email'] = $request->get('email') ?? '';
                $data['phone'] = $request->get('phone') ?? '';
                $data['property'] = $request->get('property') ?? '';
                $data['month_to_month'] = $request->get('month_to_month') ?? '';
                $data['unit_id'] = $request->get('unit') ?? '';
                $data['start_date'] = $request->get('start_date') ? new Carbon($request->get('start_date')) : new Carbon();
                $data['end_date'] = $request->get('end_date') ? new Carbon($request->get('end_date')) : null;
                $data['deleted_at'] = $request->get('end_date') ? new Carbon($request->get('end_date')) : null;
                $data['monthly_due_date'] = $request->get('due_date') ?? 1;
                $data['amount'] = (float) str_replace(",","",$request->get('amount') ?? 1);
                $data['application_id'] = $request->get('application_id') ?? null;

                $request->session()->put('lease', $data);
                if(empty($request->get('back'))){
                    $step++;
                } else {
                    $step--;
                }
                $request->session()->put('lease_add_step', $step);
                return redirect()->route('leases/add', ['step' => $step, 'application' => $data['application_id']]);
            break;

            case 2:

                if(empty($request->collectRentAssistance)){
                    unset($request['section8']);
                    unset($request['military']);
                    unset($request['other']);
                }
                if(empty($request->collectBills)){
                    $request['defaultBill'] = [];
                    $request['bill'] = [];
                }
                $bills = $request['bill'] ?? [];
                if(is_array($bills)){
                    foreach($bills as $key => $bill){
                        if(($bill['name'] == "") && ($bill['amount'] == "")) {
                            unset($bills[$key]);
                        }
                    }
                }
                $request['bill'] = $bills;
                if(empty($request->collectProratedRent)){
                    unset($request['proratedRentAmount']);
                    unset($request['proratedRentDue']);
                }
                if(empty($request->automaticLateFees)){
                    unset($request['afterDueDate']);
                    unset($request['lateFeeAmount']);
                }
                if(empty($request->collectSecurityDeposit)){
                    unset($request['securityDepositAmount']);
                    unset($request['securityDueOn']);
                    $request['moveIn'] = [];
                }

                if(empty($request->get('back'))) {
                    $attrubutes = [
                        'defaultBill.*' => 'value',
                        'bill.*.name' => 'bill name',
                        'bill.*.amount' => 'bill amount',
                        'proratedRentAmount' => 'prorated rent amount',
                        'afterDueDate' => 'after due date',
                        'lateFeeAmount' => 'late fee amount',
                        'securityDepositAmount' => 'security deposit amount',
                        'securityDueOn' => 'security due on',
                        'moveIn.*.amount' => 'move in amount',
                        'moveIn.*.due' => 'move in due',
                        'moveIn.*.memo' => 'move in memo',
                    ];

                    $request->validate(
                        [
                            'military' => ['string', 'nullable', new RentAssistance($request['military'], $request['section8'], $request['other'], $data['amount'])],
                            'section8' => ['string', 'nullable', new RentAssistance($request['military'], $request['section8'], $request['other'], $data['amount'])],
                            'other' => ['string', 'nullable', new RentAssistance($request['military'], $request['section8'], $request['other'], $data['amount'])],
                            'proratedRentDue' => '',
                            'bill.*.name' => 'required_with:collectBills|string|max:255',
                            'bill.*.amount' => 'required_with:collectBills|string',
                            'defaultBill.*' => 'string|nullable',
                            'proratedRentAmount' => 'required_with:collectProratedRent',
                            'afterDueDate' => 'required_with:automaticLateFees|string|nullable',
                            'lateFeeAmount' => 'required_with:automaticLateFees|string|nullable',
                            'securityDepositAmount' => 'required_with:securityDueOn|string|nullable',
                            'securityDueOn' => 'required_with:securityDepositAmount|date|nullable',
                            'moveIn.*.amount' => 'required_with:moveIn.*.memo|string|nullable',
                            'moveIn.*.due' => 'required_with:moveIn.*.amount|date|nullable',
                            'moveIn.*.memo' => 'required_with:moveIn.*.due|string|nullable|max:255',
                        ], [], $attrubutes
                    );
                }

                $data['defaultBill'] = $request->get('defaultBill', []);
                $data['bill'] = $request->get('bill', []);
                $data['moveIn'] = $request->get('moveIn', []);
                $data['section8'] = $request->get('section8', '');
                $data['military'] = $request->get('military', '');
                $data['other'] = $request->get('other', '');
                $data['proratedRentDue'] = $request->get('proratedRentDue', '');
                $data['proratedRentAmount'] = $request->get('proratedRentAmount', '');
                $data['afterDueDate'] = $request->get('afterDueDate', 1);
                $data['lateFeeAmount'] = $request->get('lateFeeAmount', '');
                $data['securityDepositAmount'] = $request->get('securityDepositAmount', '');
                $data['securityDueOn'] = $request->get('securityDueOn', '');
                $data['collectRentAssistance'] = $request->get('collectRentAssistance', '');

                $request->session()->put('lease', $data);
                if(empty($request->get('back'))){
                    $step++;
                } else {
                    $step--;
                }
                $request->session()->put('lease_add_step', $step);
                return redirect()->route('leases/add', ['step' => $step, 'application' => $data['application_id']]);
            break;

            case 3:
                $data['financeAccount'] = $request->get('financeAccount', "");
                $data['document_ids'] = $request->get('document_ids', []);
                $request->session()->put('lease', $data);

                if(empty($request->get('back'))) {
                    $request->validate(
                        [
                            'financeAccount' => 'required',
                        ]
                    );
                }

                if(!empty($request->get('back'))){
                    $step--;
                    $request->session()->put('lease_add_step', $step);
                    return redirect()->route('leases/add', ['step' => $step, 'application' => $data['application_id']]);
                }

                //##############################################################
                // Actually process goes here when user press "Complete move in"
                //##############################################################

                $stripeRequestSent = false;
                if($request->get('financeAccount', "") == "_new"){
                    if($request->get('financeSwitch') == 'stripe') {

                        $request->validate([
                            'account_holder_name' => 'required|max:255',
                            'stripe_account_id' => 'required|max:255',
                            'nickname' => 'required|max:255',
                        ]);

                        $landlord = Auth::user();
                        $financialNew = $landlord->financialAccounts->where('source_id', $request->stripe_account_id)->first();
                        if (empty($financial)) {
                            $financialNew = Financial::create([
                                'user_id' => $landlord->id,
                                'nickname' => $request->nickname,
                                'finance_order' => $landlord->nextFinanceOrder(),
                                'holder_name' => $request->account_holder_name,
                                'finance_type' => 'stripe_account',
                                'source_id' => $request->stripe_account_id,
                                'last4' => substr($request->stripe_account_id, -4),
                            ]);
                            $financeService = new StripeService;
                            $connectURL = $financeService->connectURL($request->stripe_account_id);
                            $landlord->notify(new ConnectStripeAccount($connectURL));
                            $stripeRequestSent = true;
                        }
                    } elseif($request->get('financeSwitch') == 'dwolla_target'){
                        $user = Auth::user();
                        $rules = [
                            'dwolla_account_holder_name' => 'required|max:255',
                            'dwolla_routing_number' => 'required|numeric|digits:9',
                            'dwolla_account_number' => 'required|numeric|digits_between:6,17',
                            'dwolla_bank_account_type' => 'required|max:255',
                        ];
                        if(empty($user->dwolla_tos)){
                            $rules['accept_tos'] = 'accepted';
                        }
                        $messages = [];
                        $attributes =  [
                            'dwolla_account_holder_name' => 'Holder Name',
                            'dwolla_routing_number' => 'Routing Number',
                            'dwolla_account_number' => 'Account Number',
                            'dwolla_bank_account_type' => 'Account Type',
                        ];
                        $this->validate($request, $rules, $messages, $attributes);

                        $user = Auth::user();
                        $identity = UserIdentity::where('user_id', $user->id)->first();
                        if(!empty($identity) && empty($identity->customer_url)){
                            $identity->delete();
                            $identity = null;
                        }

                        try{
                            $apiClient = DwollaService::getClient();
                            DwollaService::updateWebhook($apiClient);
                        } catch (\DwollaSwagger\ApiException $e) {
                            \Log::info("Dwolla Error (Connect). " . $e->getResponseBody());

                            $er = json_decode($e->getResponseBody());
                            if(!empty($er->_embedded->errors)){
                                $message = '';
                                foreach($er->_embedded->errors as $err){
                                    $message .= $err->message . ',';
                                }
                                $message = trim($message, ' ,');
                            } else {
                                $message = $er->message ?? "API Error";
                            }
                            return back()->with('dwolla-error', $message);
                        }

                        if(empty($identity)/* || ($identity->verified == 0)*/){
                            //add dwolla unverified customer
                            try{
                                $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
                                /** @noinspection PhpParamsInspection */
                                $customerUrl = $customersApi->create([
                                    'firstName' => $user->name,
                                    'lastName' => $user->lastname,
                                    'email' => $user->email,
                                    'ipAddress' => $request->ip(),
                                    //'correlationId' => $user->id,
                                ]);
                            } catch (\DwollaSwagger\ApiException $e) {
                                \Log::info("Dwolla Error (ADD Customer). " . $e->getResponseBody());
                                $er = json_decode($e->getResponseBody());
                                if(!empty($er->_embedded->errors)){
                                    $message = '';
                                    foreach($er->_embedded->errors as $err){
                                        $message .= $err->message . ',';
                                    }
                                    $message = trim($message, ' ,');
                                } else {
                                    $message = $er->message ?? "API Error";
                                }
                                return back()->with('dwolla-error', $message);
                            }
                            $identity = UserIdentity::create([
                                'user_id' => $user->id,
                                'first_name' => $user->name,
                                'last_name' => $user->lastname,
                                'email' => $user->email,
                                'address' => '',
                                'city' => '',
                                'state' => '',
                                'zip' => '',
                                'customer_url' => $customerUrl,
                                'status' => 'unverified',
                            ]);
                            //return redirect()->route('profile/finance')->with('dwolla-error', 'Please verify your account <a href="'.route("profile/identity").'">here</a>');
                        }

                        try{
                            $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
                            /** @noinspection PhpParamsInspection */
                            $fundingSourceUrl = $fsApi->createCustomerFundingSource(
                                [
                                    'routingNumber' => $request->dwolla_routing_number,
                                    'accountNumber' => $request->dwolla_account_number,
                                    'bankAccountType' => $request->dwolla_bank_account_type,
                                    'name' => $request->dwolla_account_holder_name,
                                ], $identity->customer_url
                            );
                        } catch (\DwollaSwagger\ApiException $e) {
                            \Log::info("Dwolla Error (Connect, Create Customer Funding Source). " . $e->getResponseBody());

                            $er = json_decode($e->getResponseBody());
                            if(!empty($er->_embedded->errors)){
                                $message = '';
                                foreach($er->_embedded->errors as $err){
                                    $message .= $err->message . ',';
                                }
                                $message = trim($message, ' ,');
                            } else {
                                $message = $er->message ?? "API Error";
                            }

                            $request->flash();
                            return back()->with('dwolla-error', $message);
                        }

                        if(empty($user->dwolla_tos)){
                            $timeAccepted = \Carbon\Carbon::now();
                            $user->dwolla_tos = "Accepted: ".$timeAccepted->toDateTimeString().". IP: ".$request->ip();
                            $user->save();
                        }

                        $financialNew = Financial::create([
                            'user_id' => $user->id,
                            'nickname' => $request->dwolla_account_holder_name,
                            'finance_order' => $user->nextFinanceOrder(),
                            'holder_name' => $request->dwolla_account_holder_name,
                            'finance_type' => 'dwolla_target',
                            'identity_id' => $identity->id,
                            'funding_source_url' => $fundingSourceUrl,
                            'last4' => substr($request->dwolla_account_number, -4),
                            'connected' => $identity->status == 'verified' ? 1 : 0,
                        ]);
                    } else {
                        return back();
                    }
                }

                $lease = new Lease();

                $lease->firstname = ucfirst(strtolower($data['firstname']));
                $lease->lastname = ucfirst(strtolower($data['lastname']));
                $lease->email = $data['email'];
                $lease->phone = $data['phone'];
                $lease->unit_id = $data['unit_id'];
                $lease->start_date = $data['start_date'];
                $lease->end_date = $data['end_date'];
                $lease->monthly_due_date = $data['monthly_due_date'];
                $lease->amount = $data['amount'];
                $lease->application_id = $data['application_id'];

                $lease->section8 = (float) str_replace(",","",$data['section8'] ?? 0);
                $lease->military = (float) str_replace(",","",$data['military'] ?? 0);
                $lease->other = (float) str_replace(",","",$data['other'] ?? 0);
                $lease->prorated_rent_due = $data['proratedRentDue'] ? new Carbon($data['proratedRentDue']) : null;
                $lease->prorated_rent_amount = (float) str_replace(",","",$data['proratedRentAmount'] ?? 0);
                $lease->late_fee_day = $data['afterDueDate'] ?? 1;
                $lease->late_fee_amount = (float) str_replace(",","",$data['lateFeeAmount'] ?? 0);
                $lease->security_deposit = $data['securityDueOn'] ? new Carbon($data['securityDueOn']) : null;
                $lease->security_amount = (float) str_replace(",","",$data['securityDepositAmount'] ?? 0);

                $lease->save();

                foreach ($data['defaultBill'] as $key => $value) {
                    $bill = new Bill();
                    $bill->name = Bill::find($key)->name;
                    $bill->lease_id = $lease->id;
                    $bill->parent_id = $key;
                    $bill->value = (float) str_replace(",","",$value ?? 0.00);
                    $bill->save();
                }

                foreach ($data['document_ids'] as $document_id) {
                    $document = Document::find($document_id);
                    $document->lease_id = $lease->id;
                    $document->save();
                }

                foreach ($data['bill'] as $value) {
                    if (
                        $value &&
                        isset($value['name']) &&
                        isset($value['amount'])
                    ) {
                        $bill = new Bill();
                        $bill->name = $value['name'];
                        $bill->lease_id = $lease->id;
                        $bill->parent_id = null;
                        $bill->value = (float) str_replace(",","",$value['amount']);
                        $bill->save();
                    }
                }

                foreach ($data['moveIn'] as $value) {
                    if (
                        $value &&
                        isset($value['amount']) &&
                        isset($value['due']) &&
                        isset($value['memo'])
                    ) {
                        $moveIn = new MoveIn();
                        $moveIn->memo = $value['memo'];
                        $moveIn->amount = (float) str_replace(",","",$value['amount']);
                        $moveIn->due_on = $value['due'];
                        $moveIn->lease_id = $lease->id;
                        $moveIn->save();
                    }
                }
                $this->lr->createInvoices($lease);

                if(empty($request->get('skip'))) {
                    // finance account (3-rd step)
                    $financeAccount = $request->financeAccount == 'other' ? 0 : ($request->financeAccount == '_new' ? $financialNew->id : $request->financeAccount);
                    if ($request->financeAccount != 'other') {
                        if ($fu = FinanceUnit::where([['unit_id', $lease->unit_id], ['user_id', Auth::user()->id]])->first()) {
                            $fu->update([
                                'finance_id' => $financeAccount,
                                'recurring_payment_day' => $lease->monthly_due_date
                            ]);
                        } else {
                            FinanceUnit::create([
                                'user_id' => Auth::user()->id,
                                'unit_id' => $lease->unit_id,
                                'finance_id' => $financeAccount,
                                'recurring_payment_day' => $lease->monthly_due_date
                            ]);
                        }
                    }
                }

                $role = Role::where('name', 'Tenant')->first();
                $tenant = $this->ur->createTenantIfNotIsset($data , $role, $lease->unit->property->user);
                $sendTenIfNotIsset = User::where('email', $data['email'])->whereNotNull('last_login_at')->whereHas('roles', function ($q) {
                    $q->where('name', 'Tenant');})->first();
                if ($sendTenIfNotIsset) {
                    $tenant->notify(new TenantCreatedLease($lease));
                }
//                $tenant->notify(new TenantCreatedLease($lease));

                $request->session()->forget('lease_add_step');
                $request->session()->forget('lease');
                if($stripeRequestSent){
                    return redirect()->route('properties/units/leases', ['unit' => $data['unit_id']])->with('success', 'Stripe account connect request was sent to your email. Please check your email box and and follow instruction to finish connecting your financial data.');
                }
                return redirect()->route('properties/units/leases', ['unit' => $data['unit_id']])->with('success','Your lease has been successfully created!')
                    ->with('whatsnext','You can view your upcoming and paid payments on the “Payments” screen.  There is an ability to create and send rent-specific bills to your client (for example water, gas and etc).')
                    ->with('gif', url('/').'/images/help/lease-created-whats-next.gif');
            break;

            default:
                abort(404);
            break;
        }
    }

    public function close(Request $request) {
        if (!$request->has('lease')) {
            return back();
        }

        $lease = Lease::find($request->get('lease'));

        if (empty($lease)){
            return back();
        }

        $lease->delete();

        // Notify Lanlord: landlord ended lease
        $landlord = $lease->unit->property->user;
        $landlord->notify(new LandlordEndedLease($lease));

        // Notify Tenant: tenant ended lease
        $tenant = User::where('email', $lease->email)->first();
        if ($tenant) {
            $tenant->notify(new TenantEndedLease($lease));
        }

        return redirect()->back();
    }

    public function resendEmail(Request $request) {
        if (!$request->has('lease')) {
            return back();
        }
        $lease = Lease::find($request->get('lease'));

        if (empty($lease)){
            return back();
        }
        $tenant = User::where('email', $lease->email)->first();
        $landlord = $lease->unit->property->user;
        if ($tenant) {
            $tenant->notify(new TenantWasCreated($tenant, $landlord));
        }

        //Called from different views
        return redirect()->back()->with('success','Email Successfully Sent');
    }

    public function editSave(EditLeaseRequest $request) {
        $update = [];
        $leaseUpdated = false;
        switch ($request->get('type')) {
            case 'date':
                $update = [
                    $request->get('name') => $request->get('value') ? new Carbon($request->get('value')) : null
                ];
                if ($request->get('name') == 'end_date') $update['end_date'] = $request->get('value') ? new Carbon($request->get('value')) : null;
            break;

            case 'string':
            case 'email':
            case 'phone':
                $value = $request->get('value');
                if ($request->get('name') == 'firstname' || $request->get('name') == 'lastname') {
                    $value = ucfirst(strtolower($request->get('value')));
                }
                $update = [$request->get('name') => $value];
            break;
            case 'integer':
            case 'numeric':
                $update = [$request->get('name') => (float) str_replace(",","",$request->get('value'))];
            break;

            case 'bill':
                [, $id] = preg_split('/\-/', $request->get('name'));
                $bill = Bill::find($id);

                //check if something changed
                if($bill->value != $request->get('value')){
                    $leaseUpdated = true;
                }

                $bill->value = (float) str_replace(",","",$request->get('value'));
                $bill->save();
            break;

            case 'new_bill':
                $bill = new Bill();
                $bill->name = $request->get('name');
                $bill->lease_id = intval($request->get('lease'));
                $bill->parent_id = null;
                $bill->value = (float) str_replace(",","",$request->get('value') ?? 0.00);
                $bill->save();
            break;

            case 'movein-amount':
                [, $id] = preg_split('/\-/', $request->get('name'));
                $bill = MoveIn::find($id);

                //check if something changed
                if($bill->amount != $request->get('value')){
                    $leaseUpdated = true;
                }

                $bill->amount = (float) str_replace(",","",$request->get('value'));
                $bill->save();
            break;

            case 'movein-memo':
                [, $id] = preg_split('/\-/', $request->get('name'));
                $bill = MoveIn::find($id);

                //check if something changed
                if($bill->memo != $request->get('value')){
                    $leaseUpdated = true;
                }

                $bill->memo = $request->get('value');
                $bill->save();
            break;

            case 'movein-date':
                [, $id] = preg_split('/\-/', $request->get('name'));
                $bill = MoveIn::find($id);

                //check if something changed
                if($bill->due_on != new Carbon($request->get('value'))){
                    $leaseUpdated = true;
                }

                $bill->due_on = new Carbon($request->get('value'));
                $bill->save();
            break;

            case 'new_movein':
                $movein = new MoveIn();
                $movein->due_on = new Carbon($request->get('due_on'));
                $movein->amount = (float) str_replace(",","",$request->get('amount'));
                $movein->memo = $request->get('memo');
                $movein->lease_id = intval($request->get('lease'));
                $movein->save();
            break;
        }

        if (count($update) > 0){
            $lease = Lease::where('id', $request['lease'])->withTrashed()->first();

            $validator = Validator::make($update, [
                'amount' => ['numeric','min:1', new RentAssistance($lease->military,$lease->section8,$lease->other,$request['value'])],
                'military' => ['numeric', new RentAssistance($request['value'],$lease->section8,$lease->other,$lease->amount)],
                'section8' => ['numeric', new RentAssistance($lease->military,$request['value'],$lease->other,$lease->amount)],
                'other' => ['numeric', new RentAssistance($lease->military,$lease->section8,$request['value'],$lease->amount)],

                'start_date' => [new LeaseStartDateSelf($request['unit'],$lease->end_date,$lease->id)],
                'end_date' => [new LeaseEndDateSelf($request['unit'],$lease->start_date,$lease->id)],
            ]);

            /* //this check has been moved to app\Rules
            $payedInvoices = [];
            if (isset($update['start_date'])) {
                $payedInvoices = Invoice::where([['base_id',$lease->id],['is_lease_pay',1]])
                            ->whereDate('pay_month', '<' ,$update['start_date'])
                            ->get()
                            ->filter(function($item) {
                                if ($item->payed > 0) {
                                    return $item;
                                }
                            });
            }
            if (isset($update['end_date'])) {
                $payedInvoices = Invoice::where([['base_id',$lease->id],['is_lease_pay',1]])
                            ->whereDate('pay_month', '>' ,$update['end_date'])
                            ->get()
                            ->filter(function($item) {
                                if ($item->payed > 0) {
                                    return $item;
                                }
                            });
            }
            */

            if ($validator->fails()) {
                return [
                    'error' => 'error',
                    'message' => $validator->errors()->first(),
                ];

                /* //this check has been moved to app\Rules
            } elseif (
                //  if lease start/end dates are out of updated start/end dates range and there is a payed invoice(s) for lease
                count($payedInvoices) > 0 &&
                (isset($update['start_date']) && Carbon::parse($lease->start_date)->format('Y-m') < Carbon::parse($update['start_date'])->format('Y-m')
                || isset($update['end_date']) && $lease->end_date && Carbon::parse($lease->end_date)->format('Y-m') > Carbon::parse($update['end_date'])->format('Y-m'))
                    ) {
                if (isset($update['start_date'])) {
                    $payedInvoices = Invoice::where([['base_id',$lease->id],['is_lease_pay',1]])
                                ->whereBetween('pay_month', [$lease->start_date, $update['start_date']])
                                ->get()
                                ->filter(function($item) {
                                    if ($item->payed > 0) {
                                        return $item;
                                    }
                                });
                }
                if (isset($update['end_date'])) {
                    $payedInvoices = Invoice::where([['base_id',$lease->id],['is_lease_pay',1]])
                                ->whereBetween('pay_month', [$update['end_date'], $lease->end_date])
                                ->get()
                                ->filter(function($item) {
                                    if ($item->payed > 0) {
                                        return $item;
                                    }
                                });
                }
                if (count($payedInvoices) > 0) {
                    return [
                        'error' => 'error',
                        'message' => 'You can\'t change Least Start Date and Lease End date because there was payment(s) made or there was bill(s) created during the specified date range.',
                    ];
                }
                */
            } else {
                //check if something changed
                $columns = $lease->getAttributes();
                foreach($update as $key => $val){
                    if($columns[$key] != $update[$key]){
                        $leaseUpdated = true;
                    }
                }

                Lease::withTrashed()->find($request->get('lease'))->update($update);
            }
        }

        // add created object to response
        switch ($request->get('type')) {
            case 'new_bill':
                $update = ['bill' => $bill];
                $formatted_amount = number_format($bill->value, 2, '.', ',');
                $leaseUpdated = true;
                break;
            case 'new_movein':
                $movein->due_on = Carbon::parse($movein->due_on)->format("m/d/Y");
                $update = ['movein' => $movein];
                $formatted_amount = number_format($movein->amount, 2, '.', ',');
                $leaseUpdated = true;
                break;
        }

        if($leaseUpdated){
            $lease = Lease::where('id', $request['lease'])->withTrashed()->first();
            $this->lr->createInvoices($lease);

            // Notify Tenant: changes were made to the lease
            $tenant = User::where('email', $lease->email)->first();
            if ($tenant) {
                $tenant->notify(new TenantChangedLease($lease));
            }
        }

        return [
            'name' => !empty($request->get('name')) ? $request->get('name') : '',
            'value' => !empty($request->get('value')) ? $request->get('value') : '',
            'lease' => intval($request->get('lease')),
            'update' => $update,
            'formatted_amount' => $formatted_amount ?? "",
        ];
    }

    public function documentUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['doc', 'docx', 'pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx', 'csv'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $filePath = 'public/documents/' . $user->id . '/' . $request->unit_id;
                [, $filepath] = preg_split('/\//', $document_file->store($filePath), 2);
                $document = new Document();
                $document->user_id = $user->id;
                $document->lease_id = empty($request->lease_id) ? null : $request->lease_id;
                $document->unit_id = $request->unit_id;
                $document->document_category = 8;
                $document->filepath = $filepath;
                $document->document_type = 'shared_document';
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                $target_file_path_output[] = [
                    'url' => url('storage/' . $filepath),
                    'name' => $document->name,
                    'icon' => $document->icon(),
                    'id' => $document->id
                ];

            } else {
                $target_file_path_output[] = [
                    'error' => 'File type not allowed',
                    'name' => $document_file->getClientOriginalName(),
                    'icon' => '<i class="fal fa-file"></i>',
                    'id' => '0'
                ];
            }

        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function moveInOutUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $filename  = time() . rand(100000000,999999999) . rand(100000000,999999999) . '_thumb.' . $document_file->getClientOriginalExtension();
                $filepath = 'app/public/lease/' . $filename;
                if(/*extension_loaded('gd') || */ extension_loaded('imagick')){
                    Image::configure(array('driver' => 'imagick'));
                    Image::make($document_file->getRealPath())->fit(200)->save(storage_path($filepath));
                    $thumbnailPath = 'lease/' . $filename;
                }
                $pathToFile = 'public/lease/' . $user->id . '/' . $request->unit_id;
                [, $filepath] = preg_split('/\//', $document_file->store($pathToFile), 2);

                $document = new Document();
                $document->user_id = $user->id;
                $document->lease_id = empty($request->lease_id) ? null : $request->lease_id;
                $document->unit_id = $request->unit_id;
                $document->filepath = $filepath;
                $document->thumbnailpath = $thumbnailPath ?? $filepath;
                $document->document_type = $request->document_type;
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                if(empty($request->lease_id)){
                    $count = Document::where(['document_type' => $document->document_type, 'user_id' => Auth::user()->id,])->whereNull('lease_id')->count();
                } else {
                    $count = Document::where(['document_type' => $document->document_type, 'lease_id' => $document->lease_id, 'user_id' => Auth::user()->id,])->count();
                }
                if($count > 15){
                    $document->delete();
                    $target_file_path_output[] = [
                        'error' => 'Too many files',
                        'name' => '',
                        'icon' => '<i class="fal fa-file"></i>',
                        'id' => '0'
                    ];
                    break;
                }

                $target_file_path_output[] = [
                    'url' => url('storage/' . $filepath),
                    'thumb_url' => url('storage/' . ($thumbnailPath ?? $filepath)),
                    'name' => $document->name,
                    'created_at' => \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a'),
                    'icon' => $document->icon(),
                    'id' => $document->id
                ];

            } else {
                $target_file_path_output[] = [
                    'error' => 'File type not allowed',
                    'name' => $document_file->getClientOriginalName(),
                    'icon' => '<i class="fal fa-file"></i>',
                    'id' => '0'
                ];
            }

        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function documentDelete(Request $request)
    {
        $user = Auth::user();
        $document = Document::where(['id' => $request->document_id, 'user_id' => $user->id ])->first();

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

    public function ajaxGetPropertyUnit(Request $request){
        $units = $request->property_id ? Property::find($request->property_id)->units->sortBy('name') : [];
        return view(
            'leases.add.ajax-get-property-unit',
            [
                'units' => $units,
            ]
        );
    }

}
