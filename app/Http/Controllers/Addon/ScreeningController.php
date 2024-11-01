<?php

namespace App\Http\Controllers\Addon;

use App\Models\AddonScreening;
use App\Http\Controllers\Controller;
use App\Notifications\ScreeningRequestTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Application;
use App\Models\Addon;
use App\Notifications\LandlordScreeningReady;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class ScreeningController extends Controller
{

    public function index($application_id)
    {
        return view(
            'addon.screening',
            [
                'application' => Application::find($application_id),
            ]
        );
    }

    public function adv()
    {
        return view(
            'addon.screening-adv',
            [
                'addon' => Addon::where('active', 1)->where('name', 'screening')->first(),
            ]
        );
    }

    public function send(Request $request)
    {
        if (Auth::user()->hasAddon('screening')) {
            $application = Application::find($request->application_id);

            $taz_url = env('TAZ_URL');
            $token = env('TAZ_TOKEN');
            $clientGuid = env('TAZ_CLIENT_GUID');
            $clientProductGuid = env('TAZ_CLIENT_PRODUCT_GUID');

            // Create Applicant
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $taz_url . '/v1/clients/' . $clientGuid . '/applicants',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                    "textingEnabled": false,
                    "firstName": "' . $application->firstname . '",
                    "middleName": "",
                    "noMiddleName": true,
                    "lastName": "' . $application->lastname . '",
                    "email": "' . $application->email . '"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ),
            ));

            $r = curl_exec($curl);
            $response = json_decode($r);
            curl_close($curl);

            if (empty($response->applicantGuid)) {
                \Log::info("TAZ: " . $r);
                return redirect()->route('applications')->with('error', 'Error');
            }

            $applicantGuid = $response->applicantGuid;
            $externalIdentifier = date('YmdHms') . rand(10000000, 99999999);

            // Post Order
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $taz_url . '/v1/clients/' . $clientGuid . '/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                    "applicantGuid": "' . $applicantGuid . '",
                    "clientProductGuid": "' . $clientProductGuid . '",
                    "useQuickApp": true,
                    "generalReportReference": "MYNESTHUB",
                    "externalIdentifier": "' . $externalIdentifier . '",
                    "queueConsumerDisclosure": true,
                    "quickappNotifyApplicants": false,
                    "certifyPermissiblePurpose": true
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ),
            ));

            $ro = curl_exec($curl);
            $responseOrder = json_decode($ro);
            curl_close($curl);

            if (empty($responseOrder->orderGuid)) {
                \Log::info("TAZ: " . $ro);
                return redirect()->route('applications')->with('error', 'Error');
            }

            $screening = new AddonScreening();
            $screening->user_id = Auth::user()->id;
            $screening->application_id = $application->id;
            $screening->applicantGuid = $response->applicantGuid;
            $screening->firstName = $response->firstName;
            $screening->lastName = $response->lastName;
            $screening->email = $response->email;
            $screening->log_applicant = $r;
            $screening->orderGuid = $responseOrder->orderGuid;
            $screening->orderStatus = $responseOrder->orderStatus;
            $screening->orderType = $responseOrder->orderType;
            $screening->externalIdentifier = $responseOrder->externalIdentifier;
            $screening->quickappApplicantLink = $responseOrder->quickappApplicantLink;
            $screening->log_order = $ro;
            $screening->save();

            Notification::route('mail', $screening->email)->notify(new ScreeningRequestTenant($screening->firstName, $screening->lastName, $screening->quickappApplicantLink, $screening->email));

            return redirect()->route('applications')->with('success', 'Screening request has been sent successfully.');
        }
    }

    public function callback(Request $request)
    {
        try {
            $r = $request->getContent();
        } catch (\Exception $e) {
            \Log::info("TAZ.Callback: no body data");
            return;
        }

        $response = json_decode($r);

        if (empty($response->resourceGuid)) {
            \Log::info("TAZ.Callback: " . $r);
            return;
        }

        $resourceGuid = $response->resourceGuid;

        $screening = AddonScreening::where(['orderGuid' => $resourceGuid])->first();
        if (empty($screening)) {
            \Log::info("TAZ.Callback.Resource not found:" . $resourceGuid);
            return;
        }

        $screening->log_callback = $r;
        $screening->save();

        //get order status
        $taz_url = env('TAZ_URL');
        $token = env('TAZ_TOKEN');
        $clientGuid = env('TAZ_CLIENT_GUID');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $taz_url . '/v1/clients/' . $clientGuid . '/orders/' . $resourceGuid . '/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $r = curl_exec($curl);
        $response = json_decode($r);
        curl_close($curl);

        $screening->log_callback = $screening->log_callback . "\n" . $r;

        if (empty($response->orderDetail->status)) {
            $screening->save();
            return;
        }

        $screening->orderStatus = $response->orderDetail->status;
        $screening->save();

        //get report
        $taz_url = env('TAZ_URL');
        $token = env('TAZ_TOKEN');
        $clientGuid = env('TAZ_CLIENT_GUID');

        // Order Results as PDF
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $taz_url . '/v1/clients/' . $clientGuid . '/orders/' . $resourceGuid . '/resultsPdf',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $r = curl_exec($curl);
        $response = json_decode($r);
        curl_close($curl);

        $screening->log_callback = $screening->log_callback . "\n" . $r;

        if (!empty($response->message)) {
            $screening->save();
            //comment for testing
            return;
        }

        /**/
        $screening->result_temp = trim($r, "\"'");

        $filename = $screening->id . "-" . md5(rand(10000, 99999)) . ".pdf";
        $resultPdf = trim($r, "\"'");
        $contents = file_get_contents($resultPdf);
        $filepath = "public/taz/" . $filename;
        Storage::disk('local')->put($filepath, $contents);
        $url = url(Storage::url($filepath));

        $screening->result = $url;
        /**/

        //$screening->result = trim( $r ,"\"'");
        $screening->save();

        $user = $screening->user;
        $user->notify(new LandlordScreeningReady($user, $screening));
    }

    public function test(Request $request)
    {

        if (Auth::user()->hasAddon('screening')) {
            $taz_url = env('TAZ_URL');
            $token = env('TAZ_TOKEN');

            //Clients for Order
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $taz_url . '/v1/orders/clients?page=0&size=5',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',

                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ),

            ));

            $response = curl_exec($curl);
            curl_close($curl);

            echo $response;

            $localIP = getHostByName(getHostName());
            echo ';Local IP:' . $localIP;
            return;
        }
    }

    public function callbackTest()
    {
        //test:
        //http://portal-rents.test/screening/callback-test
        //https://portal.MYNESTHUB.com/screening/callback-test
        //https://portal-rents.web-104.net/screening/callback-test

        $addonScreening = 1;
        $r = '"https://web-104.net/ascent/pdf_implementation.pdf"';
        //$r = '"https://lightning.instascreen.net/send/interchangeview/?a=dekmpo9v6qpdoa016du49tk6160721visno1zQo9yToiGUX3SjNXCVinAjoIdk5ag7XyTagEKrtK7NHGm&b=2&c=rptview&file=423753&format=pdf"';
        $screening = AddonScreening::where(['id' => $addonScreening])->first();


        /**/
        $screening->result_temp = trim($r, "\"'");

        $filename = $screening->id . "-" . md5(rand(10000, 99999)) . ".pdf";
        $resultPdf = trim($r, "\"'");
        $contents = file_get_contents($resultPdf);
        $filepath = "public/taz/" . $filename;
        Storage::disk('local')->put($filepath, $contents);
        $url = url(Storage::url($filepath));

        $screening->result = $url;
        /**/

        $screening->save();

        $user = $screening->user;
        $user->notify(new LandlordScreeningReady($user, $screening));

    }


}
