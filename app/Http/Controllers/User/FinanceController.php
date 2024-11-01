<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserIdentity;
use App\Services\DwollaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\StripeService;
use App\Models\Financial;
use App\Models\FinanceUnit;
use App\Models\Unit;
use App\Models\Lease;
use App\Notifications\ConnectStripeAccount;

class  FinanceController extends Controller
{
    private $financeService;

    public function __construct(StripeService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        if (session('adminLoginAsUser')) {
            return redirect()->route('profile');
        }

        if (!empty(session()->get('redirect_link'))) {
            $url = session()->get('redirect_link');
            session()->forget('redirect_link');
            return redirect($url);
        }

        $user = Auth::user();
        $identity = UserIdentity::where('user_id', $user->id)->where('verified', 1)->first();

        $lease = Lease::where('email', $user->email)->first();
        $tenantLeases = Lease::whereNull('deleted_at')->where('email', $user->email)->get();
        foreach ($tenantLeases as $lease) {
            if ($lease->landlordLinkedFinance()) break;
        }

        return view(
            'user.finance',
            [
                'user' => $user,
                'finance' => session('fId') ? Financial::find(session('fId')) : null,
                'units' => $user->getUnits(),
                'success' => false,
                'identity' => $identity ?? null,
                'lease' => $lease,
            ]
        );
    }

    public function getPlaidLinkToken(Request $request)
    {
        $client_id = env('PLAID_CLIENT_ID');
        $secret = env('PLAID_SECRET');
        $base_url = env('PLAID_BASE_URL');

        $plaidUniqueUserId = 'user_' . Auth::user()->id;

        $data = '{
            "client_id": "' . $client_id . '",
            "secret": "' . $secret . '",
            "client_name": "MYNESTHUB",
            "user": { "client_user_id": "' . $plaidUniqueUserId . '" },
            "products": ["auth"],
            "country_codes": ["US"],
            "language": "en"
        }';
        $url = $base_url . '/link/token/create';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $r = curl_exec($curl);
        if (!$r) {
            return response()->json(['message' => 'PLAID connection error: '], 400);
        }
        curl_close($curl);

        $result = json_decode($r);

        return response()->json([
            'link_token' => $result->link_token,
        ], 200);
    }

    public function getDwollaIavToken(Request $request)
    {
        $user = Auth::user();
        $identity = UserIdentity::where('user_id', $user->id)->first();

        try {
            $apiClient = DwollaService::getClient();

            //TODO check how it works in production and if we need it each time
            //DwollaService::updateWebhook($apiClient);

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
            return response()->json(['message' => $message . " (0)"], 400);
        }

        if (empty($identity)) {
            try {
                $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
                /** @noinspection PhpParamsInspection */
                $customerTenantUrl = $customersApi->create([
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
                return response()->json(['message' => $message . " (1)"], 400);
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
                'customer_url' => $customerTenantUrl,
            ]);
        }

        try {
            $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
            $fsToken = $customersApi->getCustomerIavToken($identity->customer_url);
        } catch (\DwollaSwagger\ApiException $e) {
            \Log::info("Dwolla Error (IAV Token). " . $e->getResponseBody());
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
            return response()->json(['message' => $message . " (2)"], 400);
        }

        $token = $fsToken->token;
        return response()->json([
            'iav_token' => $token,
        ], 200);
    }

    public function addDwollaAchTenantAccount(Request $request)
    {
        $fundingSourceUrl = $request->funding_source_url;
        \Log::info('$funding_source_url: ' . $fundingSourceUrl);

        try {
            $apiClient = DwollaService::getClient();

            $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
            $fundingSource = $fsApi->id($fundingSourceUrl);
        } catch (\DwollaSwagger\ApiException $e) {
            \Log::info("Dwolla Error (Connect, Get Funding Source). " . $e->getResponseBody());
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
            return response()->json([
                'message' => 'DWOLLA connection error. ' . $message
            ], 400);
        }

        $user = Auth::user();
        $identity = UserIdentity::where('user_id', $user->id)->first();
        $f = Financial::create([
            'user_id' => $user->id,
            'nickname' => $fundingSource->name,
            'finance_order' => $user->nextFinanceOrder(),
            'holder_name' => $user->name,
            'finance_type' => 'dwolla_source',
            'identity_id' => $identity->id,
            'funding_source_url' => $fundingSourceUrl,
            'last4' => 'XXXX',
            'connected' => 1,
        ]);

        \Log::info("Funding source object: " . serialize($fundingSource));

        Session::flash('success', "Bank added.");
        return response()->json([
            'route' => route('profile/finance'),
            'finance_id' => $f->id,
        ], 200);

    }

    public function addCheckingAccount(Request $request)
    {
        //Stripe with Plaid

        $plaid_public_token = $request->plaid_public_token;
        $plaid_account_id = $request->plaid_account_id;

        $client_id = env('PLAID_CLIENT_ID');
        $secret = env('PLAID_SECRET');
        $base_url = env('PLAID_BASE_URL');

        //=================
        $data = '{
            "client_id": "' . $client_id . '",
            "secret": "' . $secret . '",
            "public_token": "' . $plaid_public_token . '"
        }';
        $url = $base_url . '/item/public_token/exchange';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $r = curl_exec($curl);
        if (!$r) {
            return response()->json([
                'message' => 'PLAID connection error.'
            ], 400);
        }
        curl_close($curl);
        $result = json_decode($r);
        //\Log::info('plaid_access_token: '.$result->access_token);
        //\Log::info('plaid_account_id: '.$plaid_account_id);

        //=============
        $data = '{
            "client_id": "' . $client_id . '",
            "secret": "' . $secret . '",
            "access_token": "' . $result->access_token . '",
            "account_id": "' . $plaid_account_id . '"
        }';
        $url = $base_url . '/processor/stripe/bank_account_token/create';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $r = curl_exec($curl);
        if (!$r) {
            return response()->json([
                'message' => 'PLAID connection error.'
            ], 400);
        }
        curl_close($curl);
        $result = json_decode($r);
        //\Log::info('stripe_bank_account_token: '.$r);

        if (!empty($result->stripe_bank_account_token)) {
            \Log::info('Account added successfully stripe_bank_account_token: ' . $result->stripe_bank_account_token);

            $user = Auth::user();
            try {
                $this->financeService->retriveCustomer($user->customer_id);
            } catch (\Exception $e) {
                $newCustomer = $this->financeService->createCustomer($user->email);
                $user->update([
                    'customer_id' => $newCustomer->id
                ]);
            }
            try {
                // create a bank account source
                $bank_account = $this->financeService->createSource($user->customer_id, $result->stripe_bank_account_token);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'STRIPE connection error. ' . $e->getMessage(),
                ], 400);
            }

            $f = Financial::create([
                'user_id' => $user->id,
                'nickname' => "Bank Account",
                'finance_order' => $user->nextFinanceOrder(),
                'holder_name' => $user->name,
                'finance_type' => 'bank',
                'source_id' => $bank_account["id"],
                'last4' => $bank_account["last4"],
                'fingerprint' => $bank_account["fingerprint"],
                'connected' => 1,
            ]);

            Session::flash('success', "Bank added.");
            return response()->json([
                'route' => route('profile/finance'),
                'finance_id' => $f->id,
            ], 200);
        }

        return response()->json([
            'message' => 'PLAID connection error.' //$e->getMessage(),
        ], 400);
        /*
        // Manually added account workaround
        $request->validate([
            'account_holder_name' => 'required|max:255',
            'routing_number' => 'required|numeric|digits:9',
            'account_number' => 'required|numeric|confirmed|digits_between:6,17',
            'account_number_confirmation' => 'required|numeric',
            'nickname' => 'required|max:255',
        ]);

        $user = Auth::user();
        try {
            $this->financeService->retriveCustomer($user->customer_id);
        } catch (\Exception $e) {
            $newCustomer = $this->financeService->createCustomer($user->email);
            $user->update([
                'customer_id' => $newCustomer->id
            ]);
        }

        $data = $request->except(['_token', 'account_number_confirmation', 'nickname', 'form-action']);
        try {
            // create a bank account token
            $bank_token = $this->financeService->createToken('bank_account',$data);
            // create a bank account source
            $bank_account = $this->financeService->createSource($user->customer_id,$bank_token['id']);
            // verify bank account
            $this->financeService->verifySource($user->customer_id,$bank_account["id"]);

            $f = Financial::create([
                'user_id' => $user->id,
                'nickname' => $request->nickname,
                'finance_order' => $user->nextFinanceOrder(),
                'holder_name' => $request->account_holder_name,
                'finance_type' => 'bank',
                'source_id' => $bank_account["id"],
                'last4' => $bank_account["last4"],
                'fingerprint' => $bank_account["fingerprint"],
            ]);
            return response()->json([
                'route' => route('profile/finance'),
                'finance_id' => $f->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        */
    }

    public function addCardAccount(Request $request)
    {
        $request->validate([
            'cardNumber' => 'required|digits_between:15,19',
            'expiration' => 'required|date_format:m/y|after:' . \Carbon\Carbon::today()->subMonth()->format('m/y'),
            'cvv' => 'required|digits_between:3,4',
            'nameOnCard' => 'required|max:255',
            'billingAddress' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zip' => 'required|digits_between:5,6',
            'financeAccountNickname' => 'required|max:255',
        ]);

        $user = Auth::user();
        try {
            $this->financeService->retriveCustomer($user->customer_id);
        } catch (\Exception $e) {
            $newCustomer = $this->financeService->createCustomer($user->email);
            $user->update([
                'customer_id' => $newCustomer->id
            ]);
        }

        $data['number'] = $request->cardNumber;
        $data['exp_month'] = explode('/', $request->expiration)[0];
        $data['exp_year'] = explode('/', $request->expiration)[1];
        $data['cvc'] = $request->cvv;
        try {
            // create a credit card token
            $card_token = $this->financeService->createToken('card', $data);
            // create a credit card source
            $card_account = $this->financeService->createSource($user->customer_id, $card_token['id']);

            $f = Financial::create([
                'user_id' => $user->id,
                'nickname' => $request->financeAccountNickname,
                'finance_order' => $user->nextFinanceOrder(),
                'holder_name' => $request->nameOnCard,
                'finance_type' => 'card',
                'source_id' => $card_account["id"],
                'last4' => $card_account["last4"],
                'fingerprint' => $card_account["fingerprint"],
                'exp_date' => $request->expiration,
                'billing_address' => $request->billingAddress,
                'billing_address_2' => $request->billingAddress2,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]);
            return response()->json([
                'route' => route('profile/finance'),
                'finance_id' => $f->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function addPayPalAccount(Request $request)
    {
        $request->validate([
            'paypal_email' => 'required|max:64|email:rfc',
            /*'holder_name' => 'required|max:255',
            'billingAddress' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zip' => 'required|digits_between:5,6',*/
        ]);

        $user = Auth::user();

        Financial::create([
            'user_id' => $user->id,
            'nickname' => $request->paypal_email,
            'paypal_email' => $request->paypal_email,
            'finance_type' => 'paypal',
            'connected' => 1,
            /*'holder_name' => $request->holder_name,
            'billing_address' => $request->billingAddress,
            'billing_address_2' => $request->billingAddress2,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,*/
        ]);

        return redirect()->route('profile/finance')->with('success', 'PayPay Email added.');
    }

    public function replaceFinanceAccount(Request $request)
    {
        $request->validate([
            'replace_with' => 'required',
        ]);

        $f = Financial::find($request->record_id);
        $fToReplace = Financial::find($request->replace_with);

        if ($f->finance_type == $fToReplace->finance_type) {
            foreach ($f->finance_units as $fu) {
                $fu->update([
                    'finance_id' => $request->replace_with
                ]);
            }
            return redirect()->route('profile/finance');
        } else {
            return redirect()->route('profile/finance')->with('general-error', 'Only same type allowed');
        }
    }

    public function linkUnits(Request $request)
    {
        $f = Financial::find($request->record_id);
        session(['fId' => $request->record_id]);

        $request->validate([
            'linked_id' => 'present|array',
        ], [
            'linked_id.present' => 'Select units to link.'
        ]);

        $funits = $f->finance_units;
        foreach ($funits as $fu) {
            if (!in_array($fu->unit_id, $request->linked_id)) {
                //unlink landlord and tenant
                $fus = FinanceUnit::where('unit_id', $fu->unit_id)->get();
                foreach ($fus as $fu1) {
                    $fu1->delete();
                }
                //$fu = FinanceUnit::where([['finance_id',$f->id],['unit_id',$fu->unit_id]])->first();
                //$fu->delete();
            }
        }
        foreach ($request->linked_id as $uid) {
            $lease = Lease::where([['unit_id', $uid], ['email', Auth::user()->email]])->first();
            if (!$f->isLinked($uid)) {
                if ($fu = FinanceUnit::where([['unit_id', $uid], ['user_id', Auth::user()->id]])->first()) {
                    $fu->update([
                        'finance_id' => $f->id,
                        'recurring_payment_day' => $lease ? $lease->monthly_due_date : null
                    ]);
                } else {
                    FinanceUnit::create([
                        'user_id' => Auth::user()->id,
                        'finance_id' => $f->id,
                        'unit_id' => $uid,
                        'recurring_payment_day' => $lease ? $lease->monthly_due_date : null
                    ]);
                }
            }
        }
        session()->forget('fId');
        return redirect()->route('profile/finance')->with('success', 'Finance account has been updated successfully');
    }

    public function updateFinanceAccount(Request $request)
    {
        $f = Financial::find($request->record_id);
        session(['fId' => $request->record_id]);

        $validations = [];
        if ($f->finance_type == 'paypal') {
            $validations['paypal_email'] = 'required|max:64|email:rfc';
        } else {
            $validations['edit_nickname'] = 'required|max:255';
        }
        if ($f->finance_type == 'card') {
            $validations['edit_billingAddress'] = 'required|max:255';
            $validations['edit_city'] = 'required|max:255';
            $validations['edit_state'] = 'required|max:255';
            $validations['edit_zip'] = 'required|digits_between:5,6';
        }
        $request->validate($validations);

        $f->update([
            'nickname' => $f->finance_type == 'paypal' ? $request->paypal_email : $request->edit_nickname,
            'billing_address' => $f->finance_type == 'card' ? $request->edit_billingAddress : $f->billing_address,
            'billing_address_2' => $f->finance_type == 'card' ? $request->edit_billingAddress2 : $f->billing_address_2,
            'city' => $f->finance_type == 'card' ? $request->edit_city : $f->city,
            'state' => $f->finance_type == 'card' ? $request->edit_state : $f->state,
            'zip' => $f->finance_type == 'card' ? $request->edit_zip : $f->zip,
            'paypal_email' => $f->finance_type == 'paypal' ? $request->paypal_email : '',
        ]);

        session()->forget('fId');
        return redirect()->route('profile/finance');
    }

    public function removeFinanceAccount(Request $request)
    {
        $user = Auth::user();
        $f = Financial::find($request->record_id);

        if (($f->finance_type == 'dwolla_target') || ($f->finance_type == 'dwolla_source')) {

            try {
                $apiClient = DwollaService::getClient();
                //DwollaService::updateWebhook($apiClient);

                $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
                /** @noinspection PhpParamsInspection */
                $fsApi->softDelete(['removed' => true], $f->funding_source_url);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. " . $e->getResponseBody());
                $er = json_decode($e->getResponseBody());
                if (!empty($er->_embedded->errors)) {
                    $message = '';
                    foreach ($er->_embedded->errors as $err) {
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "API Error (0)";
                }

                $request->flash();
                return redirect()->route('profile/finance')->with('general-error', $message);
            }

        } else {
            try {
                $this->financeService->deleteSource($f->user->customer_id, $f->source_id);
            } catch (\Exception $e) {
            }
        }

        $finance_type = $f->finance_type;

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //unlink all tenant's accounts
            if (($f->finance_type == 'dwolla_target') || ($f->finance_type == 'stripe_account')) {
                $financeUnits = FinanceUnit::where('finance_id', $f->id)->get();
                foreach ($financeUnits as $fu1) {
                    DB::statement('DELETE FROM `finance_units` WHERE `unit_id` =:unit_id', ['unit_id' => $fu1->unit_id]);
                }
            }
        }

        $f->delete();

        //delete unverified DWOLLA Customer
        if ($finance_type == 'dwolla_source') {
            $dwollaAccountsCount = Financial::where([
                ['user_id', $user->id],
                ['finance_type', 'dwolla_source']
            ])->whereNull('deleted_at')->count();
            if ($dwollaAccountsCount == 0) {
                $identity = UserIdentity::where('user_id', $user->id)->first();
                //\Log::info("Dwolla Deactivate customer: ".$identity->customer_url);
                try {
                    $apiClient = DwollaService::getClient();
                    $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
                    /** @noinspection PhpParamsInspection */
                    $customer = $customersApi->updateCustomer(array(
                        'status' => 'deactivated',
                    ), $identity->customer_url);
                } catch (\DwollaSwagger\ApiException $e) {
                    \Log::info("Dwolla Error (Connect, Deactivate User Account). " . $e->getResponseBody());
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
                    return back()->with('error', 'DWOLLA Error. ' . $message);
                }

                $identity->delete();
                \Log::info("Dwolla Customer Deactivated. " . serialize($customer));
            }
        }

        return redirect()->route('profile/finance')->with('success', 'Financial account has been removed');
    }

    public function getLinkedUnits(Request $request)
    {
        session()->forget('fId');
        $finance = Financial::find($request->record_id);

        $units = Auth::user()->getUnits();

        return response()->json([
            'view' => view('includes.finance.linked-units-modal', compact('finance', 'units'))->render()
        ], 200);
    }

    public function editFinanceAccount(Request $request)
    {
        $finance = Financial::find($request->record_id);

        return response()->json([
            'view' => view('includes.finance.edit-modal', compact('finance'))->render()
        ], 200);
    }

    public function sendStripeConnectRequest(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|max:255',
            'stripe_account_id' => 'required|unique:financial,source_id|max:255',
            'nickname' => 'required|max:255',
        ]);

        $landlord = Auth::user();
        if ($landlord->financialAccounts->where('source_id', $request->stripe_account_id)->first()) {
            return redirect()->route('profile/finance')->withInput()->with('error', 'Stripe account alredy connected.');
        }
        $f = Financial::create([
            'user_id' => $landlord->id,
            'nickname' => $request->nickname,
            'finance_order' => $landlord->nextFinanceOrder(),
            'holder_name' => $request->account_holder_name,
            'finance_type' => 'stripe_account',
            'source_id' => $request->stripe_account_id,
            'last4' => substr($request->stripe_account_id, -4),
        ]);

        $connectURL = $this->financeService->connectURL($request->stripe_account_id);
        $landlord->notify(new ConnectStripeAccount($connectURL));

        return redirect()->route('profile/finance')->with('success', 'Stripe account connect request was sent to your email.');
    }

    public function connectStripe(Request $request)
    {
        $message = 'Something went wrong.';
        $res = 'error';

        if ($request->has('code')) {
            $account = $this->financeService->connectAccount($request->code);
            //\Log::info('connectStripe info'.$account->stripe_user_id);
            if (isset($account->stripe_user_id) && Financial::where('source_id', $account->stripe_user_id)->first()) {
                $f = Financial::where('source_id', $account->stripe_user_id)->first();
                // Set stripe account connected
                $f->update([
                    'connected' => 1
                ]);
                $message = 'Your Stripe account was successfully added to MYNESTHUB.';
                $res = 'success';
                //\Log::info('connectStripe success'.$f->id);
            } else {
                $message = $account->error_description;
                $res = 'error';
                //\Log::info('connectStripe error'.$account->error_description);
            }
        }

        $user = Auth::user();
        //First finance account? link all unlinked units
        if ($user->financialCollectRecurringAccounts()->count() == 1) {
            $units = $user->getUnits();
            foreach ($units as $unit) {
                FinanceUnit::create([
                    'user_id' => $user->id,
                    'finance_id' => $f->id,
                    'unit_id' => $unit->id,
                    'recurring_payment_day' => null
                ]);
            }
            // Note. success message located directly in the view with the link units form
            return redirect()->route('profile/finance', ['finance_id' => $f->id]);
        }

        return redirect()->route('profile/finance')->with($res, $message);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function addDwollaAchLandlordAccout(Request $request)
    {
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

            $request->flash();
            return redirect()->route('profile/finance')->with('dwolla-error', $message);
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
                return response()->json(['message' => $message . " (1)"], 400);
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
            return redirect()->route('profile/finance')->with('dwolla-error', $message);
        }

        if (empty($user->dwolla_tos)) {
            $timeAccepted = \Carbon\Carbon::now();
            $user->dwolla_tos = "Accepted: " . $timeAccepted->toDateTimeString() . ". IP: " . $request->ip();
            $user->save();
        }

        $f = Financial::create([
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

        //First finance account? link all unlinked units
        if ($user->financialCollectRecurringAccounts()->count() == 1) {
            $units = $user->getUnits();
            foreach ($units as $unit) {
                FinanceUnit::create([
                    'user_id' => $user->id,
                    'finance_id' => $f->id,
                    'unit_id' => $unit->id,
                    'recurring_payment_day' => null
                ]);
            }

            /*
            // Note. success message located directly in the view with the link units form
            return redirect()->route('profile/finance',['finance_id'=>$f->id])->with('show-linked-units-message', 'Finance account has been added successfully');
            */
        }

        if ($identity->status == 'verified') {
            return redirect()->route('profile/finance')->with('success', 'Finance account has been added successfully');
        } else {
            return redirect()->route('profile/identity')->with('success', 'Finance account has been added. Please verify your identity.');
        }
    }

}
