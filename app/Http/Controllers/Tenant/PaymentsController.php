<?php

namespace App\Http\Controllers\Tenant;

use App\Services\DwollaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Bill;
use App\Models\Invoice;
use App\Models\Financial;
use App\Models\Payment;
use App\Models\FinanceUnit;
use App\Services\StripeService;
use App\Services\PaypalIpnService;

class PaymentsController extends Controller
{
    private $financeService;

    public function __construct(StripeService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function payInvoice(Request $request)
    {
        if (!$request->has('invoice') || session('adminLoginAsUser')) {
            return redirect()->route('payments');
        }

        $invoices = Invoice::whereIn('id', $request->get('invoice'))->orderBy('id', 'asc')->get();
        $subtotal = 0;
        $invoiceNumbers = "";
        foreach ($invoices as $item) {
            $subtotal += $item->bill_amount;
            $invoiceNumbers = $invoiceNumbers . $item->id . ",";
        }
        $invoiceNumbers = rtrim($invoiceNumbers, ',');

        $invoiceNumber = $invoices[0]->id;

        // PayPal
        //fee = 2.9% + $0.30
        $paypalFee = paypalFee($subtotal);
        $paypalTotal = paypalTotal($subtotal);

        // Stripe CC
        //fee = 2.9% + $0.30
        $stripeCcFee = stripeCcFee($subtotal);
        $stripeCcTotal = stripeCcTotal($subtotal);

        // Stripe ACH Direct Debit
        //fee = 0.8% limited to $5
        $stripeAchDdFee = stripeAchDdFee($subtotal);
        $stripeAchDdTotal = stripeAchDdTotal($subtotal);

        // Dwolla ACH
        //fee = 0.5% min 5c max $5
        $dwollaAchFee = dwollaAchFee($subtotal);
        $dwollaAchTotal = dwollaAchTotal($subtotal);

        if ($invoices[0]->lease->landlordPayPalFinancial()) {
            $landlord = $invoices[0]->lease->unit->property->user;
            $tenant = Auth::user();

            // https://developer.paypal.com/docs/paypal-payments-standard/integration-guide/formbasics/
            // https://developer.paypal.com/webapps/developer/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/

            // Use the sandbox endpoint during testing.
            if (env('STRIPE_ENV') !== null && env('STRIPE_ENV') == 'sandbox') {
                $payPalUrl = "https://sandbox.paypal.com/cgi-bin/webscr";
            } else {
                $payPalUrl = "https://www.paypal.com/cgi-bin/webscr";
            }

            $payPalForm = array(
                //SYSTEM VARS
                "cmd" => "_xclick",
                "upload" => "1",
                "business" => $invoices[0]->lease->landlordPayPalFinancial()->paypal_email,
                "no_shipping" => "1",
                "no_note" => "1",
                "notify_url" => route('tenant-paypal-notify'),
                "return" => route('tenant-paypal-return'),
                "rm" => "1",
                "cancel_return" => route('payments'),
                //"image_url" =>
                //"cpp_header_image " =>
                //"cpp_logo_image" =>
                "cbt" => 'Return to MYNESTHUB.com',
                "charset" => "utf-8",

                //ORDER VARS
                "amount" => $paypalTotal,
                "order_id" => $invoiceNumber,
                "invoice" => $invoiceNumbers,
                "currency_code" => 'USD',
                "undefined_quantity" => 0,
                "item_name" => 'Invoice# ' . $invoiceNumber,
                //"item_number" => "0",

                //USER VARS
                "lc" => "US",
                "address_override" => "0",
                "first_name" => $tenant->name,
                "last_name" => $tenant->lastname,
                //"address1" =>
                //"address2" =>
                //"zip" =>
                //"city" =>
                //"state" =>
                //"country" =>
                "email" => $tenant->email,
                "night_phone_b" => $tenant->phone ?? "",
            );

        } else {
            $payPalForm = false;
            $payPalUrl = "";
        }

        return view(
            'tenant.payments.pay-invoice',
            [
                'invoices' => $invoices,
                'subtotal' => $subtotal,
                //stripe CC
                'fee' => $stripeCcFee,
                'total' => $stripeCcTotal,

                'stripeAchDdFee' => $stripeAchDdFee,
                'stripeAchDdTotal' => $stripeAchDdTotal,
                'dwollaAchFee' => $dwollaAchFee,
                'dwollaAchTotal' => $dwollaAchTotal,
                'paypalFee' => $paypalFee,
                'paypalTotal' => $paypalTotal,
                'payPalUrl' => $payPalUrl,
                'payPalForm' => $payPalForm
            ]
        );
    }

    public function paypalReturn(Request $request)
    {
        return redirect()->route('payments')->with('wait', 'Invoice(s) has been paid successfully. Please allow up to an hour for the payment to be processed.');
    }

    public function paypalNotify(Request $request)
    {
        $ipn = new PaypalIpnService();

        // Use the sandbox endpoint during testing.
        if (env('STRIPE_ENV') !== null && env('STRIPE_ENV') == 'sandbox') {
            $ipn->useSandbox();
        }

        $verified = $ipn->verifyIPN();
        if ($verified) {
            //https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/

            $invoice_str = $request['invoice'];
            $payment_status = strtoupper($request['payment_status']); // must be "Completed"
            $txn_id = $request['txn_id']; // transaction id search in DB so no duplicates
            $receiver_email = $request['receiver_email']; //must be same as "bussiness"
            $mc_gross = $request['mc_gross']; // must be equal to "amount"
            $mc_currency = $request['mc_currency']; // equall to "currency"
            $payer_email = $request['payer_email'];
            $payment_date = $request['payment_date'];
            $custom_note = $request['custom'] ?? "";
            $error = 0;

            if (empty($invoice_str)) {
                $error++;
                \Log::Info("PayPal IPN Error. Invoice ID not found. Transaction ID: " . $txn_id);
            }
            $invoice_ids = explode(",", $invoice_str);
            $invoices = Invoice::whereIn('id', $invoice_ids)->orderBy('id', 'asc')->get();
            if (empty($invoices)) {
                $error++;
                \Log::Info("PayPal IPN Error. invoices " . $invoice_str . ": was not found in DB");
            }
            $amountCheck = 0;
            foreach ($invoices as $invoice) {
                $amountCheck += $invoice->amount;
            }

            if ($mc_gross != paypalTotal($amountCheck) || $mc_currency != 'USD') {
                $error++;
                \Log::Info("PayPal IPN Error. Received amount didn't match the total. Invoice ID: " . $invoice_str . ". The amount received was: $mc_gross");
            }

            \Log::info('PayPal IPN Payment Status: ' . $payment_status);
            if (($error == 0) && ($payment_status == "COMPLETED")) {
                // Success
                foreach ($invoices as $i) {
                    \Log::info('PayPal IPN Invoice Paid: ' . $i->id . ', Payer: ' . $payer_email . ', Receiver: ' . $receiver_email . ', Amount: ' . $i->amount);

                    // $finance_id find by invoice id or $receiver_email
                    if ($i->is_lease_pay) {
                        $lease = Lease::find($i->base_id);
                    } else {
                        $bill = Bill::find($i->base_id);
                        $lease = Lease::find($bill->lease_id);
                    }
                    $landlord = $lease->unit->property->user;
                    $financeAccount = Financial::where(['user_id' => $landlord->id, 'finance_type' => 'paypal'])->first();

                    Payment::create([
                        'pay_date' => \Carbon\Carbon::now(),
                        'transaction_id' => $txn_id,
                        'amount' => $i->bill_amount,
                        'invoice_id' => $i->id,
                        'finance_id' => $financeAccount->id,
                        'processing_fee' => processingFee($i->bill_amount, 'paypal'),
                        'note' => $custom_note,
                        'payment_method' => 'PayPal'
                    ]);
                }
                \Log::info('PayPal IPN Invoice Paid Total Amount: ' . $mc_gross);


            } //no errors
        } // verified
        header("HTTP/1.1 200 OK");
        return;
    }

    public function processPayment(Request $request)
    {
        $finance_id = $request->financeAccount == '_new' ? $request->newFinanceAccount : $request->financeAccount;
        $financeAccount = Financial::find($finance_id);
        $invoices = Invoice::whereIn('id', $request->invoice);
        $description = implode(', ', $invoices->pluck('description')->toArray()) . ' from tenant ' . $invoices->get()[0]->lease->tenant;

        //Calculate amount and fee
        $amount = 0;
        foreach ($invoices->get() as $i) {
            $amount += $i->bill_amount;
        }
        $fee = processingFee($amount, $financeAccount->finance_type);

        $res = 'error';
        $message = 'Landlord has no finance account linked for this lease.';

        //Stripe
        $destination = $invoices->get()[0]->lease->landlordLinkedFinance() ? $invoices->get()[0]->lease->landlordLinkedFinance()->finance->source_id : null;
        //Dwolla
        $destination_dwolla = $invoices->get()[0]->lease->landlordLinkedFinance() ? $invoices->get()[0]->lease->landlordLinkedFinance()->finance->funding_source_url : null;

        // Pay with this finance account
        if ($destination) {

            //Stripe Payment
            $data = [
                'amount' => (int)(($amount + $fee) * 100),
                'currency' => 'usd',
                'description' => $description,
                'receipt_email' => Auth::user()->email,
                'customer' => Auth::user()->customer_id,
                'source' => $financeAccount->source_id,
                'transfer_data' => [
                    'amount' => $amount * 100,
                    'destination' => $destination,
                ],
            ];
            try {
                $charge = $this->financeService->createCharge($data);

                // Create Payments if charge is success
                foreach ($invoices->get() as $i) {
                    Payment::create([
                        'note' => $request->note,
                        'pay_date' => \Carbon\Carbon::now(),
                        'transaction_id' => $charge['id'],
                        'amount' => $i->bill_amount,
                        'invoice_id' => $i->id,
                        'finance_id' => $finance_id,
                        'payment_method' => 'stripe',
                        'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                    ]);
                }
            } catch (\Exception $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }
            $res = 'success';
            $message = 'Paid successfully';
        } elseif ($destination_dwolla) {

            //Dwolla Payment

            $correlationId = time() . '-' . rand(1000000, 9999999);

            try {
                $apiClient = DwollaService::getClient();
                DwollaService::updateWebhook($apiClient);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Connect, Webhook Update). " . $e->getResponseBody());
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
                $res = 'error';
                return redirect()->route('payments', ['lease' => $invoices->get()[0]->lease->id])->with($res, $message);
            }

            try {
                $transfer_request = [
                    '_links' =>
                        [
                            'source' => ['href' => $financeAccount->funding_source_url],
                            'destination' => ['href' => $destination_dwolla],
                        ],
                    'amount' =>
                        [
                            'currency' => 'USD',
                            'value' => number_format($amount, 2, '.', ''),
                        ],
                    'correlationId' => $correlationId
                ];
                $transferApi = new \DwollaSwagger\TransfersApi($apiClient);
                $transferUrl = $transferApi->create($transfer_request);

            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Transfer). " . $e->getResponseBody());
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
                $res = 'error';
                return redirect()->route('payments', ['lease' => $invoices->get()[0]->lease->id])->with($res, $message);
            }

            // Create Payments if charge is success
            $payments = [];
            foreach ($invoices->get() as $i) {
                $payment = Payment::create([
                    'note' => $request->note,
                    'pay_date' => \Carbon\Carbon::now(),
                    'transaction_id' => $transferUrl,
                    'amount' => $i->bill_amount,
                    'invoice_id' => $i->id,
                    'finance_id' => $finance_id,
                    'payment_method' => 'dwolla',
                    'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                    'correlation_id' => $correlationId,
                ]);
                $payments[] = $payment;
            }

            try {
                $transfer = $transferApi->byId($transferUrl);
                $status = $transfer->status;
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Check Transfer). " . $e->getResponseBody());
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

                foreach ($payments as $payment) {
                    $payment->log = $message;
                    $payment->save();
                }

                $res = 'error';
                return redirect()->route('payments', ['lease' => $invoices->get()[0]->lease->id])->with($res, $message);
            }

            foreach ($payments as $payment) {
                $payment->status = $status;
                $payment->log = json_encode($transfer);
                $payment->save();
            }

            $res = 'success';
            $message = 'Paid successfully';
        }
        return redirect()->route('payments', ['lease' => $invoices->get()[0]->lease->id])->with($res, $message);
    }

    public function recurringSetup(Request $request)
    {
        if (!$request->has('lease') || !Lease::find($request->get('lease')) || session('adminLoginAsUser')) {
            return redirect()->route('payments');
        }

        $lease = Lease::find($request->get('lease'));

        $invoices = $lease->invoices()
            ->where([['is_lease_pay', 1], ['is_late_fee', 0]])
            ->get()
            ->filter(function ($item) {
                return $item->balance < 0;
            });
        $subtotal = 0;
        foreach ($invoices as $item) {
            $subtotal += $item->bill_amount;
        }

        // Stripe CC
        //fee = 2.9% + $0.30
        $stripeCcFee = stripeCcFee($subtotal);
        $stripeCcTotal = stripeCcTotal($subtotal);

        // Stripe ACH Direct Debit
        //fee = 0.8% limited to $5
        $stripeAchDdFee = stripeAchDdFee($subtotal);
        $stripeAchDdTotal = stripeAchDdTotal($subtotal);

        // Dwolla ACH
        //fee = 0.5% min 5c max $5
        $dwollaAchFee = dwollaAchFee($subtotal);
        $dwollaAchTotal = dwollaAchTotal($subtotal);

        return view(
            'tenant.payments.recurring-setup',
            [
                'lease' => $lease,
                'invoices' => $invoices,
                'subtotal' => $subtotal,
                //stripe CC
                'fee' => $stripeCcFee,
                'total' => $stripeCcTotal,

                'stripeAchDdFee' => $stripeAchDdFee,
                'stripeAchDdTotal' => $stripeAchDdTotal,
                'dwollaAchFee' => $dwollaAchFee,
                'dwollaAchTotal' => $dwollaAchTotal,
            ]
        );
    }

    public function recurringPayment(Request $request)
    {
        $finance_id = $request->financeAccount == '_new' ? $request->newFinanceAccount : $request->financeAccount;
        $financeAccount = Financial::find($finance_id);
        $lease = Lease::find($request->lease);

        if (empty($request->invoice)) {
            return back()->withInput()->with('error', 'There is no unpaid invoices yet');
        }

        $invoices = Invoice::whereIn('id', $request->invoice);

        //Stripe
        $destination = $lease->landlordLinkedFinance() ? $lease->landlordLinkedFinance()->finance->source_id : null;
        //Dwolla
        $destination_dwolla = $invoices->get()[0]->lease->landlordLinkedFinance() ? $invoices->get()[0]->lease->landlordLinkedFinance()->finance->funding_source_url : null;

        //Calculate amount and fee
        $amount = 0;
        foreach ($invoices->get() as $i) {
            $amount += $i->bill_amount;
        }
        $fee = processingFee($amount, $financeAccount->finance_type);

        // Pay with this finance account Stripe
        if ($destination) {
            $description = implode(', ', $invoices->pluck('description')->toArray()) . ' from tenant ' . $lease->tenant;
            $data = [
                'amount' => (int)(($amount + $fee) * 100),
                'currency' => 'usd',
                'description' => $description,
                'receipt_email' => Auth::user()->email,
                'customer' => Auth::user()->customer_id,
                'source' => $financeAccount->source_id,
                'transfer_data' => [
                    'amount' => $amount * 100,
                    'destination' => $destination,
                ],
            ];
            try {
                $charge = $this->financeService->createCharge($data);

                // Create Payments if charge is success
                foreach ($invoices->get() as $i) {
                    Payment::create([
                        'note' => $request->note,
                        'pay_date' => \Carbon\Carbon::now(),
                        'transaction_id' => $charge['id'],
                        'amount' => $i->bill_amount,
                        'invoice_id' => $i->id,
                        'finance_id' => $finance_id,
                        'payment_method' => 'stripe',
                        'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                    ]);
                }
            } catch (\Exception $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }

        } elseif ($destination_dwolla) {

            //Dwolla Payment

            $correlationId = time() . '-' . rand(1000000, 9999999);

            try {
                $apiClient = DwollaService::getClient();
                DwollaService::updateWebhook($apiClient);
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Connect, Webhook Update). " . $e->getResponseBody());
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
                $res = 'error';
                return back()->withInput()->with($res, $message);
            }

            try {
                $transfer_request = [
                    '_links' =>
                        [
                            'source' => ['href' => $financeAccount->funding_source_url],
                            'destination' => ['href' => $destination_dwolla],
                        ],
                    'amount' =>
                        [
                            'currency' => 'USD',
                            'value' => number_format($amount, 2, '.', ''),
                        ],
                    'correlationId' => $correlationId
                ];
                $transferApi = new \DwollaSwagger\TransfersApi($apiClient);
                $transferUrl = $transferApi->create($transfer_request);

            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Transfer). " . $e->getResponseBody());
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
                $res = 'error';
                return back()->withInput()->with($res, $message);
            }

            // Create Payments if charge is success
            $payments = [];
            foreach ($invoices->get() as $i) {
                $payment = Payment::create([
                    'note' => $request->note,
                    'pay_date' => \Carbon\Carbon::now(),
                    'transaction_id' => $transferUrl,
                    'amount' => $i->bill_amount,
                    'invoice_id' => $i->id,
                    'finance_id' => $finance_id,
                    'payment_method' => 'dwolla',
                    'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                    'correlation_id' => $correlationId,
                ]);
                $payments[] = $payment;
            }

            try {
                $transfer = $transferApi->byId($transferUrl);
                $status = $transfer->status;
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error (Check Transfer). " . $e->getResponseBody());
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

                foreach ($payments as $payment) {
                    $payment->log = $message;
                    $payment->save();
                }

                $res = 'error';
                return back()->withInput()->with($res, $message);
            }

            foreach ($payments as $payment) {
                $payment->status = $status;
                $payment->log = json_encode($transfer);
                $payment->save();
            }
        }

        //setup cron
        if ($fu = FinanceUnit::where([['unit_id', $lease->unit_id], ['user_id', Auth::user()->id]])->first()) {
            $fu->update([
                'finance_id' => $finance_id,
                'recurring_payment_day' => $request->recurring_payment_day
            ]);
        } else {
            FinanceUnit::create([
                'user_id' => Auth::user()->id,
                'unit_id' => $lease->unit_id,
                'finance_id' => $finance_id,
                'recurring_payment_day' => $request->recurring_payment_day
            ]);
        }

        return redirect()->route('payments', ['lease' => $lease->id])->with('success', 'Setup completed successfully');
    }


    public function recurringStop(Request $request)
    {
        $f = FinanceUnit::find($request->finance_id);
        $f->delete();

        return back()->with('success', 'Recurring payment stopped successfully');
    }
}
