<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FinanceUnit;
use App\Models\Financial;
use App\Models\UserIdentityDocument;
use App\Services\DwollaService;
use App\Models\UserIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class IdentityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $identity = UserIdentity::where('user_id', $user->id)->first();

        if(!empty($identity) && ($identity->status == 'verified')) {
            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }
            return view(
                'user.identity.verified',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'classifications' => $classifications ?? [],
                ]
            );

        } elseif(!empty($identity) && ($identity->status == 'retry')) {
            switch ($identity->account_type){
                case 'soleProprietorship':
                    $formView = 'user.identity.form-retry-sole-proprietorship-partial';
                    break;
                case 'corporation':
                case 'llc':
                case 'partnership':
                    $formView = 'user.identity.form-retry-with-controller-partial';
                    break;
                case 'personal':
                default:
                    $formView = 'user.identity.form-retry-personal-partial';
                    break;
            }
            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }
            return view(
                'user.identity.retry',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'formView' => $formView,
                    'classifications' => $classifications ?? [],
                ]
            );

        } elseif(!empty($identity) && ($identity->status == 'document')) {
            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }

            return view(
                'user.identity.document',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'classifications' => $classifications ?? [],
                ]
            );

        } elseif(!empty($identity) && ($identity->status == 'suspended')) {
            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }
            return view(
                'user.identity.suspended',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'classifications' => $classifications ?? [],
                ]
            );

        } elseif(!empty($identity) && ($identity->status == 'review')) {
            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }
            return view(
                'user.identity.review',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'classifications' => $classifications ?? [],
                ]
            );

        } elseif(
            empty($identity)
            || empty($identity->customer_url)
            || empty($identity->status)
            || ($identity->status == 'unverified')
        ) {

            //index (initial state)

            if(!empty($request->account_type)){
                $account_type = $request->account_type;
            } else {
                if(!empty($identity)){
                    $account_type = $identity->account_type;
                } else {
                    $account_type = 'personal';
                }
            }
            switch ($account_type){
                case 'soleProprietorship':
                    $formView = 'user.identity.form-sole-proprietorship-partial';
                    break;
                case 'corporation':
                case 'llc':
                case 'partnership':
                    $formView = 'user.identity.form-with-controller-partial';
                    break;
                case 'personal':
                default:
                    $formView = 'user.identity.form-personal-partial';
                    break;
            }

            if($account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }

            return view(
                'user.identity.index',
                [
                    'account_type' => $account_type,
                    'user' => $user,
                    'identity' => $identity,
                    'formView' => $formView,
                    'classifications' => $classifications ?? [],
                ]
            );

        } else {
            //TODO may be create a separate error view? not sure yet

            if($identity->account_type != 'personal') {
                $classifications = DwollaService::getClassifications();
                if(!is_array($classifications)){
                    return redirect()->route('profile/identity')->with('error', $classifications);
                }
            }
            return view(
                'user.identity.suspended',
                [
                    'user' => $user,
                    'identity' => $identity,
                    'classifications' => $classifications ?? [],
                ]
            );
        }
    }

    public function save(Request $request)
    {
        switch ($request->account_type){
            case 'soleProprietorship':
                $rules = [
                    'email' => 'required|max:64|email:rfc',
                    'first_name' => 'required|max:127',
                    'last_name' => 'required|max:127',
                    'ssn' => 'required|size:4',

                    'business_name' => 'required|max:127',
                    'business_classification' => 'required',
                    'address' => 'required|max:127',
                    'ein' => 'max:9',
                    'address_2' => 'max:127',
                    'city' => 'required|max:127',
                    'state' => 'required|max:2',
                    'zip' => 'required|digits_between:5,6',
                ];
                $messages = [
                    'ssn.size' => 'Please provide last 4 digits of SSN',
                ];
                $attributes =  [];
                break;
            case 'corporation':
            case 'llc':
            case 'partnership':
            $rules = [
                'email' => 'required|max:64|email:rfc',
                'first_name' => 'required|max:127',
                'last_name' => 'required|max:127',

                'business_name' => 'required|max:127',
                'business_classification' => 'required',
                'address' => 'required|max:127',
                'ein' => 'required|max:9',
                'address_2' => 'max:127',
                'city' => 'required|max:127',
                'state' => 'required|max:2',
                'zip' => 'required|digits_between:5,6',

                'controller_first_name' => 'required|max:127',
                'controller_last_name' => 'required|max:127',
                'controller_title' => 'required|max:127',
                'controller_address' => 'required|max:127',
                'controller_address_2' => 'max:127',
                'controller_city' => 'required|max:127',
                'controller_state' => 'required|max:2',
                'controller_zip' => 'required|digits_between:5,6',
                'ssn' => 'required|size:4',
            ];
            $messages = [
                'ssn.size' => 'Please provide last 4 digits of SSN',
            ];
            $attributes =  [];
                break;
            case 'personal':
            default:
                $rules = [
                    'email' => 'required|max:64|email:rfc',
                    'first_name' => 'required|max:127',
                    'last_name' => 'required|max:127',
                    'address' => 'required|max:127',
                    'address_2' => 'max:127',
                    'city' => 'required|max:127',
                    'state' => 'required|max:2',
                    'zip' => 'required|digits_between:5,6',
                    'ssn' => 'required|size:4',
                ];
                $messages = [
                    'ssn.size' => 'Please provide last 4 digits of SSN',
                ];
                $attributes =  [];
                break;
        }

        $this->validate($request, $rules, $messages, $attributes);

        $user = Auth::user();
        $identity = UserIdentity::where('user_id', $user->id)->first();
        if(!empty($identity) && ($identity->verified == 1)){
            return redirect()->route('profile/identity');
        }

        $identityData = $request->all();
        if(empty($identityData['address_2'])){
            $identityData['address_2'] = '';
        }

        if(empty($identity)) {
            $identity = $user->userIdentity()->save(new UserIdentity($identityData));
            //UserIdentity::create($request->all());
        } else {
            $identity->fill($identityData)->save();
        }

        if($request->send_for_verification == 'yes'){
            try{
                $apiClient = DwollaService::getClient();
                DwollaService::updateWebhook($apiClient);

                $customersApi = new \DwollaSwagger\CustomersApi($apiClient);

                switch ($identity->account_type){
                    case 'soleProprietorship':
                        $customerData = [
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,

                            'type' => 'business',
                            'businessType' => 'soleProprietorship',
                            'businessName' => $identity->business_name,
                            'ein' => $identity->ein,
                            'businessClassification' => $identity->business_classification,

                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,
                            'dateOfBirth' => $identity->dob,

                            # For the first attempt, only the
                            # last 4 digits of SSN required
                            'ssn' => $identity->ssn,

                            //'correlationId' => $user->id,
                        ];
                        break;
                    case 'corporation':
                    case 'llc':
                    case 'partnership':
                        $customerData = [
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,

                            'type' => 'business',
                            'businessType' => $identity->account_type,
                            'businessName' => $identity->business_name,
                            'ein' => $identity->ein,
                            'businessClassification' => $identity->business_classification,
                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,

                            'controller' =>
                                [
                                    'firstName' => $identity->controller_first_name,
                                    'lastName'=> $identity->controller_last_name,
                                    'title' => $identity->controller_title,
                                    'dateOfBirth' => $identity->dob,
                                    'ssn' => $identity->ssn,
                                    'address' =>
                                        [
                                            'address1' => $identity->controller_address,
                                            'address2' => $identity->controller_address_2,
                                            'city' => $identity->controller_city,
                                            'stateProvinceRegion' => $identity->controller_state,
                                            'postalCode' => $identity->controller_zip,
                                            'country' => 'US'
                                        ],
                                ],
                        ];
                        break;
                    case 'personal':
                    default:
                        $customerData = [
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,
                            'type' => $identity->account_type,
                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,
                            'dateOfBirth' => $identity->dob,

                            # For the first attempt, only the
                            # last 4 digits of SSN required
                            'ssn' => $identity->ssn,

                            //'correlationId' => $user->id,
                        ];
                        break;
                }
                if(empty($identity->customer_url)){
                    $customerLandlordUrl = $customersApi->create($customerData);
                    $customer = $customersApi->getCustomer($customerLandlordUrl);
                } else {
                    $customerLandlordUrl = $identity->customer_url;
                    $customer = $customersApi->updateCustomer($customerData, $customerLandlordUrl);
                }
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. Get API, Create Customer." . $e->getResponseBody());
                $er = json_decode($e->getResponseBody());
                if(!empty($er->_embedded->errors)){
                    $message = '';
                    foreach($er->_embedded->errors as $err){
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "Connection Error. Please try again.";
                }
                return redirect()->route('profile/identity')->with('error', $message);
            }

            $identity->customer_url = $customerLandlordUrl;
            $identity->status = $customer->status; //verified?

            //save submitted fields (hash)
            $identityArray = $identity->toArray();
            $hash = UserIdentity::calculateSubmittedHash($identityArray);
            $identity->submitted = $hash;

            $identity->save();

            if(!empty($customer->_links['verify-with-document'])){
                $identity->document_required = 'Controller';
                $identity->save();
            }
            if(!empty($customer->_links['verify-business-with-document'])){
                $identity->document_required = 'Business';
                $identity->save();
            }
            if(!empty($customer->_links['verify-controller-and-business-with-document'])){
                $identity->document_required = 'Controller and Business';
                $identity->save();
            }

            if($identity->status == 'verified'){
                $identity->verified = 1;
                $identity->save();

                //connect finance accounts if exists
                $financeAccounts = Financial::where('user_id', $identity->user_id)
                    ->where('finance_type', 'dwolla_target')
                    ->whereNull('deleted_at')
                    ->get();
                foreach ($financeAccounts as $f) {
                    $f->connected = 1;
                    $f->save();
                }

                // Note. success message located directly in the view with the link units form
                $f = Financial::where('user_id', $identity->user_id)
                    ->where('finance_type', 'dwolla_target')
                    ->whereNull('deleted_at')
                    ->first();
                if(!empty($f)){
                    if($user->properties->count() > 0){
                        return redirect()->route('profile/finance',['finance_id'=>$f->id])->with('show-linked-units-message', 'Identity has been verified successfully ');
                    } else {
                        return redirect()->route('profile/finance')->with('success', 'Identity has been verified successfully ');
                    }
                } else {
                    return redirect()->route('profile/finance', ['#ach'])->with('success','Identity has been verified successfully')
                        ->with('whatsnext','Add ACH routing number etc...');
                        //->with('gif', url('/').'/images/help/lease-created-whats-next.gif');
                }

            } elseif ($identity->status == 'retry'){
                return redirect()->route('profile/identity')->with('error', 'Identity has not been verified. Please review all fields and provide a full 9-digits SSN below');
            } elseif ($identity->status == 'document') {
                return redirect()->route('profile/identity')->with('error', 'Identity has not been verified. Please upload an additional document.');
            } else {
                return redirect()->route('profile/identity')->with('error', 'Verification Failed');
            }
        } else {
            return redirect()->route('profile/identity')->with('success', 'Identity saved. Please review and submit for verification');
        }
    }

    public function retry(Request $request)
    {
        switch ($request->account_type){
            case 'soleProprietorship':
                $rules = [
                    'email' => 'required|max:64|email:rfc',
                    'first_name' => 'required|max:127',
                    'last_name' => 'required|max:127',
                    'ssn' => 'required|size:11',

                    'business_name' => 'required|max:127',
                    'business_classification' => 'required',
                    'address' => 'required|max:127',
                    'ein' => 'max:9',
                    'address_2' => 'max:127',
                    'city' => 'required|max:127',
                    'state' => 'required|max:2',
                    'zip' => 'required|digits_between:5,6',
                ];
                $messages = [
                    'ssn.size' => 'Please provide the full 9 digit SSN',
                ];
                $attributes =  [];
                break;
            case 'corporation':
            case 'llc':
            case 'partnership':
            $rules = [
                'email' => 'required|max:64|email:rfc',
                'first_name' => 'required|max:127',
                'last_name' => 'required|max:127',

                'business_name' => 'required|max:127',
                'business_classification' => 'required',
                'address' => 'required|max:127',
                'ein' => 'required|max:9',
                'address_2' => 'max:127',
                'city' => 'required|max:127',
                'state' => 'required|max:2',
                'zip' => 'required|digits_between:5,6',

                'controller_first_name' => 'required|max:127',
                'controller_last_name' => 'required|max:127',
                'controller_title' => 'required|max:127',
                'controller_address' => 'required|max:127',
                'controller_address_2' => 'max:127',
                'controller_city' => 'required|max:127',
                'controller_state' => 'required|max:2',
                'controller_zip' => 'required|digits_between:5,6',
                'ssn' => 'required|size:11',
            ];
            $messages = [
                'ssn.size' => 'Please provide the full 9 digit SSN',
            ];
            $attributes =  [];
                break;
            case 'personal':
            default:
                $rules = [
                    'email' => 'required|max:64|email:rfc',
                    'first_name' => 'required|max:127',
                    'last_name' => 'required|max:127',
                    'address' => 'required|max:127',
                    'address_2' => 'max:127',
                    'city' => 'required|max:127',
                    'state' => 'required|max:2',
                    'zip' => 'required|digits_between:5,6',
                    'ssn' => 'required|size:11',
                ];
                $messages = [
                    'ssn.size' => 'Please provide the full 9 digit SSN',
                ];
                $attributes =  [];
                break;
        }
        $this->validate($request, $rules, $messages, $attributes);

        $user = Auth::user();
        $identity = UserIdentity::where('id', $request->identity_id)->where('user_id', $user->id)->first();
        if(empty($identity)){
            return redirect()->route('profile/identity');
        }

        $identityData = $request->all();
        if(empty($identityData['address_2'])){
            $identityData['address_2'] = '';
        }
        $fullSsn = $identityData['ssn'];
        $identityData['ssn'] = substr($identityData['ssn'], -4);

        $identity->fill($identityData)->save();

        if($request->send_for_verification == 'yes'){

            $identityData['account_type'] = $identity->account_type;
            $hash = UserIdentity::calculateSubmittedHash($identityData);
            if($identity->submitted == $hash){
                return redirect()->route('profile/identity')->with('error', "Please don't submit the same information");
            }

            try{
                $apiClient = DwollaService::getClient();
                DwollaService::updateWebhook($apiClient);

                $customersApi = new \DwollaSwagger\CustomersApi($apiClient);

                switch ($identity->account_type){
                    case 'soleProprietorship':
                        /** @noinspection PhpParamsInspection */
                        $retryCustomer = $customersApi->updateCustomer([
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,
                            'ipAddress' => $request->ip(),

                            'type' => 'business',
                            'businessType' => 'soleProprietorship',
                            'businessName' => $identity->business_name,
                            'ein' => $identity->ein,
                            'businessClassification' => $identity->business_classification,

                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,
                            'dateOfBirth' => $identity->dob,

                            'ssn' => $fullSsn,
                        ], $identity->customer_url);
                        break;
                    case 'corporation':
                    case 'llc':
                    case 'partnership':
                        /** @noinspection PhpParamsInspection */
                        $retryCustomer = $customersApi->updateCustomer([
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,
                            'ipAddress' => $request->ip(),

                            'type' => 'business',
                            'businessType' => $identity->account_type,
                            'businessName' => $identity->business_name,
                            'ein' => $identity->ein,
                            'businessClassification' => $identity->business_classification,
                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,

                            'controller' =>
                                [
                                    'firstName' => $identity->controller_first_name,
                                    'lastName'=> $identity->controller_last_name,
                                    'title' => $identity->controller_title,
                                    'dateOfBirth' => $identity->dob,
                                    'ssn' => $fullSsn,
                                    'address' =>
                                        [
                                            'address1' => $identity->controller_address,
                                            'address2' => $identity->controller_address_2,
                                            'city' => $identity->controller_city,
                                            'stateProvinceRegion' => $identity->controller_state,
                                            'postalCode' => $identity->controller_zip,
                                            'country' => 'US'
                                        ],
                                ],
                        ], $identity->customer_url);
                        break;
                    case 'personal':
                    default:
                        /** @noinspection PhpParamsInspection */
                        $retryCustomer = $customersApi->updateCustomer([
                            'firstName' => $identity->first_name,
                            'lastName' => $identity->first_name,
                            'email' => $identity->email,
                            'ipAddress' => $request->ip(),
                            'type' => $identity->account_type,
                            'address1' => $identity->address,
                            'address2' => $identity->address_2,
                            'city' => $identity->city,
                            'state' => $identity->state,
                            'postalCode' => $identity->zip,
                            'dateOfBirth' => $identity->dob,
                            'ssn' => $fullSsn,
                        ], $identity->customer_url);
                        break;
                }
                $customer = $customersApi->getCustomer($identity->customer_url);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. Get API, Retry." . $e->getResponseBody());
                $er = json_decode($e->getResponseBody());
                if(!empty($er->_embedded->errors)){
                    $message = '';
                    foreach($er->_embedded->errors as $err){
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "Connection Error. Please try again.";
                }
                return redirect()->route('profile/identity')->with('error', $message);
            }

            $identity->status = $customer->status;

            //save submitted fields (hash)
            $identityArray = $identity->toArray();
            $hash = UserIdentity::calculateSubmittedHash($identityArray);
            $identity->submitted = $hash;

            $identity->save();

            if(!empty($customer->_links['verify-with-document'])){
                $identity->document_required = 'Controller';
                $identity->save();
            }
            if(!empty($customer->_links['verify-business-with-document'])){
                $identity->document_required = 'Business';
                $identity->save();
            }
            if(!empty($customer->_links['verify-controller-and-business-with-document'])){
                $identity->document_required = 'Controller and Business';
                $identity->save();
            }

            if($identity->status == 'verified'){
                $identity->verified = 1;
                $identity->save();

                //connect finance accounts if exists
                $financeAccounts = Financial::where('user_id', $identity->user_id)
                    ->where('finance_type', 'dwolla_target')
                    ->whereNull('deleted_at')
                    ->get();
                foreach ($financeAccounts as $f) {
                    $f->connected = 1;
                    $f->save();
                }

                // Note. success message located directly in the view with the link units form
                $f = Financial::where('user_id', $identity->user_id)
                    ->where('finance_type', 'dwolla_target')
                    ->whereNull('deleted_at')
                    ->first();
                if(!empty($f)){
                    if($user->properties->count() > 0){
                        return redirect()->route('profile/finance',['finance_id'=>$f->id])->with('show-linked-units-message', 'Identity has been verified successfully ');
                    } else {
                        return redirect()->route('profile/finance')->with('success', 'Identity has been verified successfully ');
                    }
                } else {
                    return redirect()->route('profile/finance', ['#ach'])->with('success','Identity has been verified successfully')
                        ->with('whatsnext','Add ACH routing number etc...');
                    //->with('gif', url('/').'/images/help/lease-created-whats-next.gif');
                }

            } elseif ($identity->status == 'retry'){
                return redirect()->route('profile/identity')->with('error', 'Identity has not been verified. Please review all fields and provide a full 9-digits SSN');
            } elseif ($identity->status == 'document') {
                return redirect()->route('profile/identity')->with('error', 'Identity has not been verified. Please upload an additional document.');
            } else {
                return redirect()->route('profile/identity')->with('error', 'Verification Failed');
            }
        } else {
            return redirect()->route('profile/identity')->with('success', 'Identity saved. Please review and submit for verification');
        }
    }

    public function document(Request $request)
    {
        $user = Auth::user();
        $identity = UserIdentity::where('id', $request->identity_id)->where('user_id', $user->id)->first();
        if(empty($identity)){
            return redirect()->route('profile/identity');
        }

        if($request->send_for_verification == 'yes'){

            $document = UserIdentityDocument::where(['user_identity_id' => $identity->id, 'status' => 'ready'])->first();
            if(empty($document)){
                return redirect()->route('profile/identity')->with('error', "Please upload the document");
            }

            $documentType = $request->document_type;

            $target_url = $identity->customer_url . '/documents';

            try{
                $api_key = env('DWOLLA_API_KEY');
                $api_secret = env('DWOLLA_API_SECRET');
                $api_url = env('DWOLLA_API_URL');
                \DwollaSwagger\Configuration::$username = $api_key;
                \DwollaSwagger\Configuration::$password = $api_secret;
                $apiClient = new \DwollaSwagger\ApiClient($api_url);
                $tokensApi = new \DwollaSwagger\TokensApi($apiClient);
                $appToken = $tokensApi->token();
                \DwollaSwagger\Configuration::$access_token = $appToken->access_token;
                DwollaService::updateWebhook($apiClient);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. Get API" . $e->getResponseBody());

                $er = json_decode($e->getResponseBody());
                if(!empty($er->_embedded->errors)){
                    $message = '';
                    foreach($er->_embedded->errors as $err){
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "Connection Error. Please try again.";
                }
                return redirect()->route('profile/identity')->with('error', $message);
            }

            $decrypted = Crypt::decrypt(Storage::get('private/' . $document->filepath));
            $tempFileName = $document->id . '-' . rand(100000,999999) . '.' . $document->extension;
            Storage::put('temp/' . $tempFileName, $decrypted);
            //$file_name_with_full_path = realpath( storage_path() . '/app/public/' . $document->filepath );
            $file_name_with_full_path = realpath( storage_path() . '/app/temp/' . $tempFileName );

            try{
                $post = array(
                    'documentType' => $documentType,
                    'file'=> curl_file_create($file_name_with_full_path)
                );
                $ch = curl_init();
                $headers = array(
                    'Content-type: multipart/form-data',
                    'Authorization: Bearer '. $appToken->access_token,
                    'Accept: application/vnd.dwolla.v1.hal+json',
                );
                curl_setopt($ch, CURLOPT_URL,$target_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                    function($curl, $header) use (&$headers)
                    {
                        $len = strlen($header);
                        $header = explode(':', $header, 2);
                        if (count($header) < 2) // ignore invalid headers
                            return $len;
                        $headers[strtolower(trim($header[0]))][] = trim($header[1]);
                        return $len;
                    }
                );
                $result = json_decode(curl_exec($ch));
                $message = @$result->message;
                //\Log::info("Dwolla. Doc uploaded. Headers:".print_r($headers,true));
                $dwolla_document_url = @$headers['location'][0];

                Storage::delete('temp/' . $tempFileName);
                if(!empty($message)){
                    \Log::info("Dwolla. Doc upload Error. Result:".print_r($result,true));
                    return redirect()->route('profile/identity')->with('error', $message);
                }
            } catch (\DwollaSwagger\ApiException $e) {
                Storage::delete('temp/' . $tempFileName);
                \Log::info("Dwolla Error. Upload Document" . $e->getResponseBody());

                $er = json_decode($e->getResponseBody());
                if(!empty($er->_embedded->errors)){
                    $message = '';
                    foreach($er->_embedded->errors as $err){
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "Connection Error. Please try again.";
                }
                return redirect()->route('profile/identity')->with('error', $message);
            }

            $document->status = "review";
            $document->dwolla_document_url = $dwolla_document_url;
            $document->save();

            $identity->status = "review";
            $identity->save();

            return redirect()->route('profile/identity')->with('success', 'Your document has been sent for verification successfully');
        } else {
            return redirect()->route('profile/identity')->with('success', 'Identity saved. Please review and submit for verification');
        }
    }

    public function unverify(Request $request)
    {
        $user = Auth::user();
        if (Auth::user()->isTenant()){
            return redirect()->route('profile/finance')->with('general-error', 'Error');
        }
        $identity_id = $request->identity_id;
        $identity = UserIdentity::where('id', $identity_id)->where('user_id', $user->id)->first();

        if(!empty($identity)){
            //remove financial accounts

            try{
                $apiClient = DwollaService::getClient();
                $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. Get API" . $e->getResponseBody());
                $er = json_decode($e->getResponseBody());
                if(!empty($er->_embedded->errors)){
                    $message = '';
                    foreach($er->_embedded->errors as $err){
                        $message .= $err->message . ',';
                    }
                    $message = trim($message, ' ,');
                } else {
                    $message = $er->message ?? "Connection Error. Please try again. (0)";
                }

                $request->flash();
                return redirect()->route('profile/identity')->with('general-error', $message);
            }

            $financialAccounts = Financial::where('user_id', $user->id)->where('finance_type','dwolla_target')->get();
            foreach($financialAccounts as $f){
                try{
                    /** @noinspection PhpParamsInspection */
                    $fsApi->softDelete(['removed' => true ], $f->funding_source_url);
                } catch (\DwollaSwagger\ApiException $e) {
                    \Log::info("Dwolla Error. Delete Financial Account" . $e->getResponseBody());
                    $er = json_decode($e->getResponseBody());
                    if(!empty($er->_embedded->errors)){
                        $message = '';
                        foreach($er->_embedded->errors as $err){
                            $message .= $err->message . ',';
                        }
                        $message = trim($message, ' ,');
                    } else {
                        $message = $er->message ?? "Connection Error. Please try again. (1)";
                    }

                    $request->flash();
                    return redirect()->route('profile/identity')->with('general-error', $message);
                }

                $financeUnits = FinanceUnit::where('finance_id', $f->id)->get();
                foreach ($financeUnits as $fu1) {
                    DB::statement( 'DELETE FROM `finance_units` WHERE `unit_id` =:unit_id', ['unit_id' => $fu1->unit_id] );
                }
                $f->delete();
            }

            \Log::info("Dwolla Deactivate customer: ".$identity->customer_url);
            try {
                $apiClient = DwollaService::getClient();
                $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
                /** @noinspection PhpParamsInspection */
                $customer = $customersApi->updateCustomer(array (
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
                    $message = $er->message ?? "Connection Error. Please try again.";
                }
                return back()->with('error', 'Error. ' . $message);
            }

            $documents = UserIdentityDocument::where('user_identity_id', $identity->id)->get();
            foreach ($documents as $document){
                Storage::delete('private/' . $document->filepath);
                if(!empty($document->thumbnailpath)){
                    Storage::delete('private/' . $document->thumbnailpath);
                }
                $document->delete();
            }
            $identity->delete();
            \Log::info("Dwolla Customer Deactivated. " . serialize($customer));
        }
        return redirect()->route('profile/finance')->with('success', 'Verification Cancelled');
    }

    public function documentUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $fileContent = File::get($document_file);
                $encrypted = Crypt::encrypt($fileContent);

            //    $filePath = 'identity/' . $user->id;
            //    [, $filepath] = preg_split('/\//', $document_file->store($filePath), 2);

                $filepath = 'identity/' . $user->id . "/" . md5(rand(1,9999999)) . "." . strtolower($document_file->getClientOriginalExtension());
                Storage::put('private/' . $filepath, $encrypted);

                $document = new UserIdentityDocument();
                $document->user_identity_id = $request->user_identity_id;
                $document->filepath = $filepath;
                $document->document_type = '';
                $document->status = 'ready';
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                $fileViewPath = route('profile/identity/view-document',['id_hash' => md5($document->id) . "." . $document->extension, 'nocache' => rand(1,999999)]);
                $target_file_path_output[] = [
                    'url' => $fileViewPath, //url('storage/' . $filepath),
                    'name' => $document->name,
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

    public function documentView($id_hash){
        $user = Auth::user();
        [$nameHash,] = explode('.',$id_hash);
        $userIdentity = UserIdentity::where('user_id', $user->id)->first();
        $document = UserIdentityDocument::where('user_identity_id', $userIdentity->id)
            ->whereRaw('MD5(id) = ?',[$nameHash])
            ->first();
        if(empty($document)){
            abort(404);
        }
        $decrypted = Crypt::decrypt(Storage::get('private/' . $document->filepath));
        return response($decrypted)->header('Content-Type', $document->mime);
    }

    public function documentDelete(Request $request)
    {
        $user = Auth::user();
        $userIdentity = UserIdentity::where('user_id', $user->id)->first();
        $document = UserIdentityDocument::where(['id' => $request->document_id])
            ->where('user_identity_id', $userIdentity->id)
            ->first();

        if(!empty($document)) {
            $document_id = $document->id;

            Storage::delete('private/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('private/' . $document->thumbnailpath);
            }
            $document->delete();

            $output = ['success' => 'Processed', 'document_id' => $document_id];
            return response()->json($output);
        }
    }

}
