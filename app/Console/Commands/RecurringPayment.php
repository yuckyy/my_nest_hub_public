<?php

namespace App\Console\Commands;

use App\Services\DwollaService;
use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Models\Lease;
use App\Models\FinanceUnit;
use App\Models\Payment;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use App\Notifications\FailureProcessingTransaction;
use App\Services\StripeService;

class RecurringPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:recurring-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $lr;
    private $financeService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LeasesRepositoryInterface $lr, StripeService $financeService)
    {
        parent::__construct();

        $this->lr = $lr;
        $this->financeService = $financeService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Process Recurring Payments");

        // get all leases with financeUnit's recurring_payment_day = tomorrow
        $fUnits = FinanceUnit::where('recurring_payment_day',Carbon::today()->format('d'))->get();

        foreach ($fUnits as $fu) {
            $tenant = $fu->user;
            $leases = Lease::where('email',$tenant->email)->get();
            foreach ($leases as $lease) {
                $landlord = $lease->landlord;
                $invoices = [];
                $leaseInvoices = $lease->invoices()->where([['is_lease_pay',1],['is_late_fee',0]])->get();
                foreach ($leaseInvoices as $item) {
                    if ($item->balance < 0) {
                        $invoices []= $item;
                    }
                }

                //Source Finance Account
                $financeAccount = $fu->finance;

                $amount = 0;
                foreach ($invoices as $i) {
                    $amount += $i->bill_amount;
                }
                $description = 'Monthly Rent from tenant ' . $lease->tenant;
                $fee = processingFee($amount, $financeAccount->finance_type);

                //Stripe
                $destination = $lease->landlordLinkedFinance() ? $lease->landlordLinkedFinance()->finance->source_id : null;
                //Dwolla
                $destination_dwolla = $lease->landlordLinkedFinance() ? $lease->landlordLinkedFinance()->finance->funding_source_url : null;

                if($amount == 0){
                    return;
                }

                if ($destination) {
                    $data = [
                        'amount' => (int) (($amount + $fee)*100),
                        'currency' => 'usd',
                        'description' => $description,
                        'receipt_email' => $tenant->email,
                        'customer' => $tenant->customer_id,
                        'source' => $financeAccount->source_id,
                        'transfer_data' => [
                            'amount' => $amount * 100,
                            'destination' => $destination,
                        ],
                    ];
                    try {
                        $charge = $this->financeService->createCharge($data);
                        // Create Payments if charge is success
                        foreach ($invoices as $i) {
                            Payment::create([
                                'pay_date' => Carbon::now(),
                                'transaction_id' => $charge['id'],
                                'amount' => $i->bill_amount,
                                'invoice_id' => $i->id,
                                'finance_id' => $fu->finance_id,
                                'payment_method' => 'stripe recurring',
                                'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                            ]);

                            \Log::info("Invoice: ".$i->id);

                        }
                    } catch (\Exception $e) {
                        // Notify to tenant if payment is not success
                        $tenant->notify(new FailureProcessingTransaction($lease, true));
                    }

                } elseif($destination_dwolla) {

                    //Dwolla Payment

                    $correlationId = time() . '-' . rand(1000000, 9999999);

                    try {
                        $apiClient = DwollaService::getClient();
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

                        \Log::info("Dwolla recurring payment. " . json_encode($transfer_request));


                        $transferApi = new \DwollaSwagger\TransfersApi($apiClient);
                        $transferUrl = $transferApi->create($transfer_request);

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
                            $message = $er->message ?? "API Error";
                        }
                        // Notify to tenant if payment is not success
                        $tenant->notify(new FailureProcessingTransaction($lease, true));
                        \Log::info("Process Recurring Payment Error. ". $message);
                    }

                    if(!empty($transferUrl)){
                        // Create Payments if charge is success
                        $payments = [];
                        foreach ($invoices as $i) {
                            $payment = Payment::create([
                                'pay_date' => Carbon::now(),
                                'transaction_id' => $transferUrl,
                                'amount' => $i->bill_amount,
                                'invoice_id' => $i->id,
                                'finance_id' => $fu->finance_id,
                                'payment_method' => 'dwolla recurring',
                                'processing_fee' => processingFee($i->bill_amount, $financeAccount->finance_type),
                                'correlation_id' => $correlationId,
                            ]);
                            $payments[] = $payment;
                        }

                        try {
                            $transfer = $transferApi->byId($transferUrl);
                            $status = $transfer->status;
                            foreach ($payments as $payment){
                                $payment->status = $status;
                                $payment->log = json_encode($transfer);
                                $payment->save();
                            }
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
                                $message = $er->message ?? "API Error";
                            }

                            foreach ($payments as $payment){
                                $payment->log = $message;
                                $payment->save();
                            }

                            // Notify to tenant if payment is not success
                            $tenant->notify(new FailureProcessingTransaction($lease, true));
                            \Log::info("Process Recurring Payment Error. ". $message);
                        }

                    }

                }




            }
        }

        \Log::info("Process Recurring Payments Completed");
    }
}
