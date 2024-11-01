<?php

namespace App\Http\Controllers\Properties\Units;

use App\Models\UserIdentity;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\DwollaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\File;
use App\Models\User;
use App\Notifications\TenantNewBill;
use App\Models\FinanceUnit;
use App\Models\Financial;
use App\Services\StripeService;
use App\Notifications\ConnectStripeAccount;

use Storage;

class PaymentsController extends Controller
{
    private $ar;

    public function __construct(ApplicationsRepositoryInterface $ar)
    {
        $this->ar = $ar;
    }

    public function index($unit, Request $request)
    {

        $unitID = ['unit_id' => $unit];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
        $applicationsCount = $applicationsData['applications']->count();

        $unitObject = Unit::find($unit);
        if (!$unitObject) {
            abort(404);
        }

        $user = Auth::user();

        $activeLeases = Lease::where('unit_id', $unit)->get();
        $inactiveLeases = Lease::onlyTrashed()->where('unit_id', $unit)->get();

        if ($request['lease']) {
            $selectedLease = Lease::withTrashed()
                ->where('id', $request['lease'])
                ->first();
        } else {
            $selectedLease = $activeLeases->first();
            if (!$selectedLease) {
                $selectedLease = $unitObject->leases->first();
            }
        }

        if (!empty($selectedLease)) {
            $fu = FinanceUnit::where([['unit_id', $selectedLease->unit->id], ['user_id', $selectedLease->landlord->id]])->first();
            if (
                $fu
                && $fu->finance
                && (
                    ($fu->finance->connected == 1) || ($fu->finance->finance_type == 'dwolla_target')
                )
            ) {
                $financeUnit = $fu;
            } else {
                $financeUnit = false;
            }
        } else {
            $financeUnit = false;
        }

        $financeAccount = $financeUnit ? $financeUnit->finance : false;

        return view(
            'properties.units.payments.index',
            [
                'applicationsCount' => $applicationsCount,
                'user' => $user,
                'unit' => $unitObject,
                'defaultBills' => Bill::where('lease_id', null)->cursor(),

                'selectedLease' => $selectedLease ?? false,
                'activeLeases' => $activeLeases ?? false,
                'inactiveLeases' => $inactiveLeases ?? false,

                'financeAccount' => $financeAccount,
            ]
        );
    }

    /*
    public function removeBill(Request $request)
    {
        $bill = Bill::find($request->bill_id);

        if($bill->file_id){
            $document = $bill->file;
            Storage::delete('public/bill/' . $document->filename);
            $document->delete();
        }
        $bill->delete();

        return back();
    }
    */

    public function addBill(Request $request)
    {
        $rules = [
            'bill_type' => 'required|string|min:1',
            'bill_amount' => 'required|min:1',
            'bill_due' => 'required|string|min:1',
        ];

        if ($request->bill_type == '_new') {
            $rules['bill_name'] = 'required|string|min:1';
        }

        $request->validate($rules);

        $bill = new Bill();
        $bill->name = $request->bill_name ? $request->bill_name : Bill::find($request->bill_type)->name;
        $bill->lease_id = $request->lease_id;
        $bill->parent_id = $request->bill_type != '_new' ? $request->bill_type : null;
        $bill->value = (float)str_replace(",", "", $request->bill_amount);
        $bill->due_date = $request->bill_due;
        $bill->bill_mode = 'additional';

        if ($bill->save()) {
            $tenant = User::where('email', $bill->lease->email)->first();
            $tenant->notify(new TenantNewBill($bill));
            // create new invoice for additional bill
            Invoice::create([
                'base_id' => $bill->id,
                'is_lease_pay' => 0,
                'is_late_fee' => 0,
                'due_date' => $bill->due_date,
                'description' => ucfirst(strtolower($bill->name)),
                'amount' => $bill->value,
            ]);

            if ($request->hasFile('bill_file')) {
                [, , $filename] = preg_split('/\//', $request->file('bill_file')->store('public/bill'));

                $document = new File();
                $document->filename = $filename;
                $document->save();

                $bill->file_id = $document->id;
                $bill->save();
            }
        }

        //return back();
        return redirect()->route('properties/units/payments', ['unit' => $request->unit_id, 'lease' => $request->lease_id,])
            ->with('success', 'Bill has been successfully added.')
            ->with('whatsnext', 'If tenant is registered in MYNESTHUB, our platform will send an email notification to the tenant about added bill. Tenant will also have an ability to view given bill on our platform.');
    }

    public function viewPayments(Request $request)
    {
        $invoice = Invoice::find($request->id);

        return response()->json([
            'view' => view('includes.units.view-payments-modal', compact('invoice'))->render()
        ], 200);
    }

    public function editPayments(Request $request)
    {
        $invoice = Invoice::find($request->id);

        return response()->json([
            'view' => view('includes.units.add-payments-modal', compact('invoice'))->render()
        ], 200);
    }

    public function addPayment(Request $request)
    {
        $request->validate(
            [
                'payment_paid_on' => 'required|string|min:1',
                'paid_amount' => 'required|min:1',
            ]
        );

        $payment = new Payment();
        $payment->invoice_id = $request->invoice_id;
        $payment->pay_date = $request->payment_paid_on;
        $payment->amount = (float)str_replace(",", "", $request->paid_amount);
        $payment->note = $request->payment_note;
        $payment->payment_method = 'Manually';
        $payment->save();

        return back();
    }

    public function markAsPaid(Request $request)
    {
        $invoices = Invoice::whereIn('id', $request->invoice);
        // Create Payments
        foreach ($invoices->get() as $i) {
            $lease = $i->leaseWithTrashed();
            if ($lease->unit->property->user->id == Auth::user()->id) {
                Payment::create([
                    'pay_date' => \Carbon\Carbon::now(),
                    'amount' => $i->bill_amount,
                    'invoice_id' => $i->id,
                ]);
            }
        }
        return back()->with('success', 'Paid successfully!');
    }

    public function removeInvoice(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);
        if ($invoice->is_lease_pay == 0) {
            $bill = $invoice->base;
            if ($bill->file_id) {
                $document = $bill->file;
                Storage::delete('public/bill/' . $document->filename);
                $document->delete();
            }
            $bill->delete();
        }
        $invoice->delete();
        return back();
    }

    public function changeFinanceAccount(Request $request)
    {
        $fu = FinanceUnit::where([['unit_id', $request->unit_id], ['user_id', Auth::user()->id]])->first();
        if (!empty($fu)) {
            if ($request->financeAccount == "") {
                //unlink landlord and tenants
                $fus = FinanceUnit::where('unit_id', $request->unit_id)->get();
                foreach ($fus as $fu1) {
                    $fu1->delete();
                }
            } else {
                $lease = Lease::find($request->lease_id);
                $fu->update([
                    'finance_id' => $request->financeAccount,
                    'recurring_payment_day' => $lease->monthly_due_date
                ]);
                //unlink tenants
                $fus = FinanceUnit::where('unit_id', $request->unit_id)->where('user_id', '!=', Auth::user()->id)->get();
                foreach ($fus as $fu1) {
                    $fu1->delete();
                }
            }
        } else {
            if ($request->financeAccount == "_new") {

                $lease = Lease::find($request->lease_id);

                $stripeRequestSent = false;
                $landlord = Auth::user();

                if ($request->get('financeSwitch') == 'stripe') {
                    $request->validate([
                        'account_holder_name' => 'required|max:255',
                        'stripe_account_id' => 'required|max:255',
                        'nickname' => 'required|max:255',
                    ]);

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

                    FinanceUnit::create([
                        'user_id' => $landlord->id,
                        'unit_id' => $lease->unit_id,
                        'finance_id' => $financialNew->id,
                        'recurring_payment_day' => $lease->monthly_due_date
                    ]);
                    return back()->with('success', 'Stripe account connect request was sent to your email.');

                } elseif ($request->get('financeSwitch') == 'dwolla_target') {
                    $user = Auth::user();

                    $rules = [
                        'dwolla_account_holder_name' => 'required|max:255',
                        'dwolla_routing_number' => 'required|numeric|digits:9',
                        'dwolla_account_number' => 'required|numeric|digits_between:6,17',
                        'dwolla_bank_account_type' => 'required|max:255',
                    ];
                    if (empty($user->dwolla_tos)) {
                        $rules['accept_tos'] = 'accepted';
                    }
                    $messages = [];
                    $attributes = [
                        'dwolla_account_holder_name' => 'Holder Name',
                        'dwolla_routing_number' => 'Routing Number',
                        'dwolla_account_number' => 'Account Number',
                        'dwolla_bank_account_type' => 'Account Type',
                    ];
                    $this->validate($request, $rules, $messages, $attributes);

                    $identity = UserIdentity::where('user_id', $user->id)->first();
                    if (!empty($identity) && empty($identity->customer_url)) {
                        $identity->delete();
                        $identity = null;
                    }

                    try {
                        $apiClient = DwollaService::getClient();
                        DwollaService::updateWebhook($apiClient);
                    } catch (\DwollaSwagger\ApiException $e) {
                        \Log::info("Dwolla Error (Connect). " . $e->getResponseBody());

                        $er = json_decode($e->getResponseBody());
                        if (!empty($er->_embedded->errors)) {
                            $message = '';
                            foreach ($er->_embedded->errors as $err) {
                                $message .= $err->message . ',';
                            }
                            $message = trim($message, ' ,');
                        } else {
                            $message = $er->message ?? "API Error";
                        }
                        return back()->with('dwolla-error', $message);
                    }

                    if (empty($identity)/* || ($identity->verified == 0)*/) {
                        //add dwolla unverified customer
                        try {
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
                            if (!empty($er->_embedded->errors)) {
                                $message = '';
                                foreach ($er->_embedded->errors as $err) {
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

                    try {
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
                        if (!empty($er->_embedded->errors)) {
                            $message = '';
                            foreach ($er->_embedded->errors as $err) {
                                $message .= $err->message . ',';
                            }
                            $message = trim($message, ' ,');
                        } else {
                            $message = $er->message ?? "API Error";
                        }

                        $request->flash();
                        return back()->with('dwolla-error', $message);
                    }

                    if (empty($user->dwolla_tos)) {
                        $timeAccepted = \Carbon\Carbon::now();
                        $user->dwolla_tos = "Accepted: " . $timeAccepted->toDateTimeString() . ". IP: " . $request->ip();
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

                    //delete old links
                    $fus = FinanceUnit::where('unit_id', $lease->unit_id)->where('user_id', $landlord->id)->get();
                    foreach ($fus as $fu1) {
                        $fu1->delete();
                    }
                    FinanceUnit::create([
                        'user_id' => $landlord->id,
                        'unit_id' => $lease->unit_id,
                        'finance_id' => $financialNew->id,
                        'recurring_payment_day' => $lease->monthly_due_date
                    ]);
                    if ($identity->status == 'verified') {
                        return back()->with('success', 'ACH Account has been added');
                    } else {
                        //return back()->with('success','ACH Account has been added');
                        return back()
                            ->with('success', 'ACH Account has been added')
                            ->with('whatsnext', 'Your identity has not been verified yet. Please verify your identity')
                            //->with('gif', url('/').'/images/help/unit-created-movein-tenant.gif')
                            ->with('whatsnext_button_text', 'Verify Your Identity')
                            ->with('whatsnext_button_url', route('profile/identity'));
                    }

                }


            } else {
                $lease = Lease::find($request->lease_id);
                $financial = Financial::find($request->financeAccount);
                if (!empty($financial)) {
                    FinanceUnit::create([
                        'user_id' => Auth::user()->id,
                        'unit_id' => $lease->unit_id,
                        'finance_id' => $financial->id,
                        'recurring_payment_day' => $lease->monthly_due_date
                    ]);
                }
            }
        }

        return back()->with('success', 'Updated successfully');

    }

}
