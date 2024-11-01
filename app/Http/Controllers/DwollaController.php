<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserIdentity;
use App\Models\UserIdentityDocument;
use App\Notifications\DwollaCustomerVerificationDocumentFailed;
use App\Notifications\DwollaCustomerVerificationSuccess;
use App\Notifications\FailureProcessingTransaction;
use App\Services\DwollaService;
use Illuminate\Http\Request;

class DwollaController extends Controller
{

    public function dwollaWebhook()
    {
        $request_body = file_get_contents('php://input');
        \Log::info("Dwolla Webhook: " . $request_body);
        $data = json_decode($request_body);
        if ($data) {

            //https://developers.dwolla.com/guides/personal-verified-customer/handle-verification-statuses#verification-statuses

            if ($data->topic == 'customer_verified') {
                $customerUrl = $data->_links->customer->href;
                $customerId = $data->resourceId;
                \Log::info("Dwolla Webhook Customer Verified: " . $customerUrl);

                $identity = UserIdentity::where('customer_url', $customerUrl)->first();
                if (empty($identity)) {
                    \Log::info("Dwolla Webhook Customer not found in DB: " . $customerUrl);
                    return response('Record Not Created Yet', 503)->header('Content-Type', 'text/plain');
                }
                if ($identity->status != 'verified') {
                    $identity->status = 'verified';
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

                    $user = User::find($identity->user_id);
                    $user->notify(new DwollaCustomerVerificationSuccess($user, $identity));
                }
            }

            if ($data->topic == 'customer_suspended') {
                $customerUrl = $data->_links->customer->href;
                \Log::info("Dwolla Webhook Customer Suspended: " . $customerUrl);
                $identity = UserIdentity::where('customer_url', $customerUrl)->first();
                if (empty($identity)) {
                    \Log::info("Dwolla Webhook Customer not found in DB: " . $customerUrl);
                    return response('Record Not Created Yet', 503)->header('Content-Type', 'text/plain');
                }
                $identity->status = 'suspended';
                $identity->verified = 0;
                $identity->save();

                $user = User::find($identity->user_id);
                $user->notify(new DwollaCustomerSuspended($user, $identity));
            }

            /*
            if ($data->topic == 'customer_reverification_needed') {
                $customerUrl = $data->_links->customer->href;
                \Log::info("Dwolla Webhook Customer Reverification_Needed: ". $customerUrl);
                $identity = UserIdentity::where('customer_url', $customerUrl)->first();
                if(empty($identity)){
                    \Log::info("Dwolla Webhook Customer not found in DB: ". $customerUrl);
                    return response('Record Not Created Yet', 503)->header('Content-Type', 'text/plain');
                }
                $identity->status = 'retry';
                $identity->verified = 0;
                $identity->save();
            }
            */

            if ($data->topic == 'customer_verification_document_failed') {
                $documentUrl = $data->_links->resource->href;
                \Log::info("Dwolla Webhook Document Failed: " . $documentUrl);
                $document = UserIdentityDocument::where('dwolla_document_url', $documentUrl)->first();
                if (empty($document)) {
                    \Log::info("Dwolla Webhook Document not found in DB: " . $documentUrl);
                    return response('Record Not Created Yet', 503)->header('Content-Type', 'text/plain');
                }
                if ($document->status != 'failed') {
                    $document->status = 'failed';
                    $document->save();

                    $identity = UserIdentity::where('id', $document->user_identity_id)->first();
                    if ($identity->status == 'review') {
                        $identity->status = 'document';
                        $identity->save();
                    }

                    //get failure reason
                    $apiClient = DwollaService::getClient();
                    $documentsApi = new \DwollaSwagger\DocumentsApi($apiClient);
                    $dwollaDocument = $documentsApi->getDocument($documentUrl);
                    //\Log::info("Dwolla Webhook. Get Document: ".print_r($dwollaDocument,true));

                    $reason = "";
                    if (!empty($dwollaDocument->all_failure_reasons)) {
                        foreach ($dwollaDocument->all_failure_reasons as $failureReason) {
                            $reason .= $failureReason->description . ". ";
                        }
                    } else {
                        switch ($dwollaDocument->failure_reason ?? '') {
                            case "ScanNotReadable":
                                $reason = "Image blurry, too dark, or obscured by glare";
                                break;
                            case "ScanNotUploaded":
                                $reason = "Scan not uploaded";
                                break;
                            case "ScanIdExpired":
                                $reason = "ID is expired";
                                break;
                            case "ScanIdTypeNotSupported":
                                $reason = "ID may be a military ID, firearm license, or other unsupported ID type";
                                break;
                            case "ScanNameMismatch":
                                $reason = "Name mismatch";
                                break;
                            case "ScanDobMismatch":
                                $reason = "Date of birth mismatch";
                                break;
                            case "ScanFailedOther":
                                $reason = "ID may be fraudulent or a generic example ID image";
                                break;
                            default:
                                $reason = $dwollaDocument->failure_reason;
                        }
                    }
                    $document->failure_description = $reason;
                    $document->save();

                    $user = User::find($identity->user_id);
                    $user->notify(new DwollaCustomerVerificationDocumentFailed($user, $identity));
                }
            }

            if ($data->topic == 'customer_verification_document_approved') {
                $documentUrl = $data->_links->resource->href;
                \Log::info("Dwolla Webhook Document Approved: " . $documentUrl);
                $document = UserIdentityDocument::where('dwolla_document_url', $documentUrl)->first();
                if (empty($document)) {
                    \Log::info("Dwolla Webhook Document not found in DB: " . $documentUrl);
                    return response('Record Not Created Yet', 503)->header('Content-Type', 'text/plain');
                }
                $document->status = 'approved';
                $document->save();

                //$identity = UserIdentity::where('id', $document->user_identity_id)->first();
                //$identity->status = 'retry';
                //$identity->verified = 0;
                //$identity->save();
            }

            if ($data->topic == 'customer_funding_source_verified') {
                \Log::info("Dwolla Webhook Funding Source Verified");
                $customerUrl = $data->_links->customer->href;
                $fundingSourceUrl = $data->_links->resource->href;
                \Log::info($customerUrl);
                \Log::info($fundingSourceUrl);

                //get fs info
                $apiClient = DwollaService::getClient();

                // $fundingSourceUrl = 'https://api-sandbox.dwolla.com/funding-sources/a2aa3fa1-734b-46a9-a192-27d154f4e6a9';

                $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
                $fundingSource = $fsApi->id($fundingSourceUrl);
                \Log::info($fundingSource->name);

                //TODO find in DwollaCustomers by $customerUrl and add $fundingSourceHref and name (add finance account)

                //\Log::info("-----------");
                //$v = var_export($fundingSource, true);
                //\Log::info("-----------");
                //\Log::info($v);
            }

            if ($data->topic == 'customer_transfer_completed') {
                \Log::info("Dwolla Webhook Transfer Completed");
                $transferUrl = $data->_links->resource->href;

                $payments = Payment::where('transaction_id', $transferUrl)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'processed') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'processed'; //processed, failed, or cancelled
                        $payment->save();
                    }
                }
            }
            if ($data->topic == 'customer_bank_transfer_completed') {
                \Log::info("Dwolla Webhook BANK Transfer Completed");
                $transferUrl = $data->_links->resource->href;

                $apiClient = DwollaService::getClient();
                $transferApi = new \DwollaSwagger\TransfersApi($apiClient);

                $transfer = $transferApi->byId($transferUrl);
                $correlationId = $transfer->correlation_id;

                $payments = Payment::where('correlation_id', $correlationId)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'processed') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'processed'; //processed, failed, or cancelled
                        $payment->save();

                        $invoice = $payment->invoice;
                        $eventLine = \Carbon\Carbon::now()->format("m/d/Y H:i:s") . " Completed. ACH ID: " . $transfer->individual_ach_id . "<br>";
                        $invoice->transaction_history = $invoice->transaction_history . $eventLine;
                        $invoice->save();

                        /*
                        \Log::info('===========');
                        \Log::info('BANK Transfer: '.print_r($transfer, true));
                        \Log::info('===========');
                        \Log::info('Payment record: '.print_r($payment, true));
                        \Log::info('===========');
                        */
                    } else {
                        $invoice = $payment->invoice;
                        if (strpos($invoice->transaction_history, "Completed") === false) {
                            $eventLine = \Carbon\Carbon::now()->format("m/d/Y H:i:s") . " Completed. ACH ID: " . $transfer->individual_ach_id . "<br>";
                            $invoice->transaction_history = $invoice->transaction_history . $eventLine;
                            $invoice->save();
                        }
                    }
                }
            }

            if ($data->topic == 'customer_transfer_failed') {
                \Log::info("Dwolla Webhook Transfer Failed");
                $transferUrl = $data->_links->resource->href;

                $payments = Payment::where('transaction_id', $transferUrl)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'failed') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'failed'; //processed, failed, or cancelled
                        $payment->save();
                        try {
                            $payment->moveToFailedPayments();
                        } catch (\Exception $e) {
                            break;
                        }
                        $payment->delete();

                        $lease = $payment->invoice->leaseWithTrashed();
                        $tenant = $payment->finance->user;
                        $recurring = $payment->payment_method == 'dwolla recurring';
                        $tenant->notify(new FailureProcessingTransaction($lease, $recurring));
                    }
                }
            }
            if (
                ($data->topic == 'customer_bank_transfer_failed') ||
                ($data->topic == 'customer_bank_transfer_creation_failed')
            ) {
                \Log::info("Dwolla Webhook BANK Transfer Failed");
                $transferUrl = $data->_links->resource->href;

                $apiClient = DwollaService::getClient();
                $transferApi = new \DwollaSwagger\TransfersApi($apiClient);

                $transfer = $transferApi->byId($transferUrl);
                $correlationId = $transfer->correlation_id;

                $payments = Payment::where('correlation_id', $correlationId)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'failed') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'failed'; //processed, failed, or cancelled
                        $payment->save();
                        try {
                            $payment->moveToFailedPayments();
                        } catch (\Exception $e) {
                            break;
                        }
                        $payment->delete();

                        $invoice = $payment->invoice;
                        $eventLine = \Carbon\Carbon::now()->format("m/d/Y H:i:s") . " Failed. ACH ID: " . $transfer->individual_ach_id . "<br>";
                        $invoice->transaction_history = $invoice->transaction_history . $eventLine;
                        $invoice->save();

                        $lease = $payment->invoice->leaseWithTrashed();
                        $tenant = $payment->finance->user;
                        $recurring = $payment->payment_method == 'dwolla recurring';
                        $tenant->notify(new FailureProcessingTransaction($lease, $recurring));

                        //\Log::info('BANK Transfer: '.print_r($transfer, true));
                    }
                }
            }

            if ($data->topic == 'customer_transfer_cancelled') {
                \Log::info("Dwolla Webhook Transfer Cancelled");
                $transferUrl = $data->_links->resource->href;

                $payments = Payment::where('transaction_id', $transferUrl)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'cancelled') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'cancelled'; //processed, failed, or cancelled
                        $payment->save();
                        try {
                            $payment->moveToFailedPayments();
                        } catch (\Exception $e) {
                            break;
                        }
                        $payment->delete();

                        $lease = $payment->invoice->leaseWithTrashed();
                        $tenant = $payment->finance->user;
                        $recurring = $payment->payment_method == 'dwolla recurring';
                        $tenant->notify(new FailureProcessingTransaction($lease, $recurring));
                    }
                }
            }
            if ($data->topic == 'customer_bank_transfer_cancelled') {
                \Log::info("Dwolla Webhook BANK Transfer Cancelled");
                $transferUrl = $data->_links->resource->href;

                $apiClient = DwollaService::getClient();
                $transferApi = new \DwollaSwagger\TransfersApi($apiClient);

                $transfer = $transferApi->byId($transferUrl);
                $correlationId = $transfer->correlation_id;

                $payments = Payment::where('correlation_id', $correlationId)->get();
                foreach ($payments as $payment) {
                    if ($payment->status != 'cancelled') {
                        $payment->log = $payment->log . '\n Webhook: ' . json_encode($data);
                        $payment->status = 'cancelled'; //processed, failed, or cancelled
                        $payment->save();
                        try {
                            $payment->moveToFailedPayments();
                        } catch (\Exception $e) {
                            break;
                        }
                        $payment->delete();

                        $invoice = $payment->invoice;
                        $eventLine = \Carbon\Carbon::now()->format("m/d/Y H:i:s") . " Cancelled. ACH ID: " . $transfer->individual_ach_id . "<br>";
                        $invoice->transaction_history = $invoice->transaction_history . $eventLine;
                        $invoice->save();

                        $lease = $payment->invoice->leaseWithTrashed();
                        $tenant = $payment->finance->user;
                        $recurring = $payment->payment_method == 'dwolla recurring';
                        $tenant->notify(new FailureProcessingTransaction($lease, $recurring));

                        //\Log::info('BANK Transfer: '.print_r($transfer, true));
                    }
                }
            }


        }
        return;
    }


    // =================
    // Below for testing
    // =================

    public function dwollaWebhookUpdate()
    {
        //   http://portal-rents.test/dwolla/webhook/update
        /*
        if (Auth::user()->email !== 'admin@MYNESTHUB.com') {
            echo 'access denied';
            return;
        }
        */

        try {
            $apiClient = DwollaService::getClient();
            DwollaService::updateWebhook($apiClient);
        } catch (\DwollaSwagger\ApiException $e) {
            //echo 'Caught exception: ',  $e->getResponseBody(), "\n";
            $er = json_decode($e->getResponseBody());
            //echo $er->message;
            return redirect()->route('dashboard')->with('error', $er->message ?? "API Error");
        }

        return;
    }


    public function dwollaLandlordCreate()
    {
        /*
        if (Auth::user()->email !== 'admin@MYNESTHUB.com') {
            echo 'access denied';
            return;
        }
        */

        $apiClient = DwollaService::getClient();

        $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
        $customerLandlordUrl = $customersApi->create([
            'firstName' => 'Johna',
            'lastName' => 'Doena',
            'email' => 'jdoena@nomaill.net',
            'type' => 'personal',
            'address1' => '99-99 33rd Stna',
            'city' => 'Some City1na',
            'state' => 'NY',
            'postalCode' => '11101',
            'dateOfBirth' => '1970-05-07',

            # For the first attempt, only the
            # last 4 digits of SSN required

            # If the entire SSN is provided,
            # it will still be accepted
            'ssn' => '1240',

            //'correlationId' => $user->id,
        ]);

        echo "<br>=======================<br>";
        var_dump($customerLandlordUrl);
        echo "<br>=======================<br>";

        \Log::info("Dwolla Landlord Created");

        //$customerLandlordUrl = 'https://api-sandbox.dwolla.com/customers/2f42ae98-b538-4c8c-bf55-4f8335cf46bb';
        //TODO store $customerLandlordUrl and all entered information except ssn

        $fsApi = new \DwollaSwagger\FundingsourcesApi($apiClient);
        $fundingSourceUrl = $fsApi->createCustomerFundingSource(
            [
                'routingNumber' => '222222226',
                'accountNumber' => '123456789',
                'bankAccountType' => 'checking',
                'name' => 'Jane Merchant',
            ], $customerLandlordUrl
        );

        echo "<br>=======================<br>";
        var_dump($fundingSourceUrl);
        echo "<br>=======================<br>";

        // $fundingSourceUrl = 'https://api-sandbox.dwolla.com/funding-sources/58cd9630-94a1-471e-89a8-18bd674657c7';
        //TODO store $fundingSourceUrl and all entered information


        $customer = $customersApi->getCustomer($customerLandlordUrl);
        echo "<br>=======================<br>";
        var_dump($customer->status);
        echo "<br>=======================<br>";

        //TODO handle status. if 'verified' - ok

        return;
    }

    public function dwollaTenantCreate(Request $request)
    {
        /*
        if (Auth::user()->email !== 'admin@MYNESTHUB.com') {
            echo 'access denied';
            return;
        }
        */

        $apiClient = DwollaService::getClient();

        $customersApi = new \DwollaSwagger\CustomersApi($apiClient);
        /*
        $customerTenantUrl = $customersApi->create([
            'firstName' => 'Joe',
            'lastName' => 'Buyer',
            'email' => 'jbuyer@mail.net',
            'ipAddress' => $request->ip(),

            //'correlationId' => $user->id,
        ]);

        echo "<br>=======================<br>";
        var_dump($customerTenantUrl);
        echo "<br>=======================<br>";

        \Log::info("Dwolla Tenant Created");
        */
        $customerTenantUrl = 'https://api-sandbox.dwolla.com/customers/b2215dab-27c3-460b-8169-17b4620294d3';
        //TODO store $customerTenantUrl and all entered information except ssn

        $fsToken = $customersApi->getCustomerIavToken($customerTenantUrl);
        $token = $fsToken->token;

        return view(
            'dwolla.iav',
            [
                'token' => $token,
            ]
        );

        // iaw return
        // {"_links":{"funding-source":{"href":"https://api-sandbox.dwolla.com/funding-sources/80275e83-1f9d-4bf7-8816-2ddcd5ffc197"}}}

    }


    public function dwollaTransfer()
    {
        /*
        if (Auth::user()->email !== 'admin@MYNESTHUB.com') {
            echo 'access denied';
            return;
        }
        */

        $apiClient = DwollaService::getClient();
        //\DwollaSwagger\Configuration::$debug = 1;

        $tenantFundingSourceUrl = 'https://api-sandbox.dwolla.com/funding-sources/a2aa3fa1-734b-46a9-a192-27d154f4e6a9';
        $landlordFundingSourceUrl = 'https://api-sandbox.dwolla.com/funding-sources/58cd9630-94a1-471e-89a8-18bd674657c7';

        $transfer_request = [
            '_links' =>
                [
                    'source' => ['href' => $tenantFundingSourceUrl],
                    'destination' => ['href' => $landlordFundingSourceUrl],
                ],
            'amount' =>
                [
                    'currency' => 'USD',
                    'value' => '225.00',
                ]
        ];
        $transferApi = new \DwollaSwagger\TransfersApi($apiClient);

        try {
            $transferUrl = $transferApi->create($transfer_request);
        } catch (\DwollaSwagger\ApiException $e) {
            //echo 'Caught exception: ',  $e->getResponseBody(), "\n";
            $er = json_decode($e->getResponseBody());
            echo $er->message;
            return;
        }

        var_dump($transferUrl);

        //$transferUrl = 'https://api-sandbox.dwolla.com/transfers/5fd97b6f-b6e5-eb11-8134-d050ab358a03';

        $transfer = $transferApi->byId($transferUrl);
        $status = $transfer->status;

        var_dump($status);


    }


}
