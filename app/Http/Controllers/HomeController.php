<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\AddonScreening;
use App\Notifications\LandlordScreeningReady;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth'/*, 'verified'*/]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $roleid = count($user->roles()->get()) > 0 ? $user->roles()->get()[0]->id : 0;

        switch ($roleid) {
            case 1:
                $route = 'admin';
                break;

            case 2:
                $route = 'landlord';
                break;

            case 3:
                $route = 'manager';
                break;

            case 4:
                $route = 'tenant';
                break;

            default:
                $route = 'tenant';
        }

        return redirect()->route($route);
    }


    public function taz()
    {
        //get basic info about the TazWorks produats
        if (Auth::user()->email !== 'admin@MYNESTHUB.com') {
            echo 'access denied';
            return;
        }

        $token = env('TAZ_TOKEN');
        $taz_url = env('TAZ_URL');

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

        echo $response . "<br /><br /><br />";

        $response_dec = json_decode($response);
        $clientGuid = $response_dec[0]->clientGuid;

        //=============================================

        //Client Products for Order

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $taz_url . '/v1/orders/clients/' . $clientGuid . '/products?page=0&size=30',
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

        $response = curl_exec($curl);
        curl_close($curl);

        echo $response . "<br /><br /><br />";

        //===================================
        // don't delete. use as guide
        /*
                // Create Applicant

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    //CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/{{client-guid}}/applicants',
                    CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/1166b165-f8f5-4718-b9d4-82b04f14f7e6/applicants',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
            "textingEnabled": false,
            "firstName": "Joe",
            "middleName": "",
            "noMiddleName": true,
            "lastName": "Clean",
            "ssn": "111-22-3333",
            "email": "john_clean@somegmail.com"
        }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;

        /*

        {"applicantGuid":"bd902c01-5ccb-47d8-8a6c-a2874e052b7a","textingEnabled":false,"firstName":"Ivan","middleName":"","noMiddleName":true,"lastName":"Kozytskyy","email":"ivankozy11111@gmail.com","phoneNumber":"(555) 565-1111","createdDate":1616141337914,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","modifiedDate":1616141337914,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","version":0,"addresses":[],"employment":[],"education":[],"aliases":[],"professionalLicenses":[],"references":[]}

        ---

        {"applicantGuid":"3b2fb986-999f-4d0a-a808-506179e857bc","textingEnabled":false,"firstName":"John","middleName":"","noMiddleName":true,"lastName":"Doe","email":"john_doe@gmail.com","phoneNumber":"(555) 565-2222","createdDate":1616171525077,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","modifiedDate":1616171525077,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","version":0,"addresses":[],"employment":[],"education":[],"aliases":[],"professionalLicenses":[],"references":[]}

        ---

        {"applicantGuid":"79f22d2a-47ea-4e3c-bb2a-1cef8abc44c2","textingEnabled":false,"firstName":"Joe","middleName":"","noMiddleName":true,"lastName":"Clean","ssn":"XXX-XX-3333","email":"john_clean@somegmail.com","createdDate":1616658759482,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","modifiedDate":1616658759482,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","version":0,"addresses":[],"employment":[],"education":[],"aliases":[],"professionalLicenses":[],"references":[]}

        */
        /*
                //All Applicants

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    //CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/{{client-guid}}/applicants?page=0&size=30',
                    CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/1166b165-f8f5-4718-b9d4-82b04f14f7e6/applicants?page=0&size=30',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;

        */
        /*

        // Post Order

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    //CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/{{client-guid}}/orders',
                    CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/1166b165-f8f5-4718-b9d4-82b04f14f7e6/orders',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
            "applicantGuid": "79f22d2a-47ea-4e3c-bb2a-1cef8abc44c2",
            "clientProductGuid": "f741fbce-b62d-428a-bc8a-0975104f8578",
            "useQuickApp": true,
            "generalReportReference": "Mid-Group",
            "externalIdentifier": "newInentificator1",
            "queueConsumerDisclosure": "true",
            "quickappNotifyApplicants": "false",
            "certifyPermissiblePurpose": true
        }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;


                /*
                {"orderGuid":"df29fa2e-7556-4b45-94f5-7c2ed2ee8eec",
                "fileNumber":410828,
                "orderStatus":"app-pending",
                "orderType":"Employment",
                "orderedDate":1616141596000,
                "generalReportReference":"Mid-Group",
                "externalIdentifier":"10001a",
                "applicantName":"KOZYTSKYY, IVAN",
                "clientName":"Sandbox API account for MYNESTHUB",
                "clientCode":"424ac743-5ed8-448d-a2c6-6e86f7f55791",
                "productName":"TazAPI - Tenant Product",
                "requestedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent",
                "searchFlagged":false,
                "quickappApplicantLink":"https://lightning.instascreen.net/orderquickapp/index.taz?x=8sa9gr3eueibv2vj4mcbli7u9b24a022i6cbdQxKfcO5bLAf4RRMK7CDRPKIYjKPihnR4j9HoP1sFY8Mr&y=410828&z=1",
                "createdDate":1616141596000,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent",
                "modifiedDate":1616141596303,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent"}

                ============

                {"orderGuid":"5aa2b738-40e7-43b5-95d3-3fb1f2ec7cca","fileNumber":410943,"orderStatus":"app-pending","orderType":"Employment","orderedDate":1616171587000,"generalReportReference":"Mid-Group","externalIdentifier":"newInentificator1","applicantName":"DOE, JOHN","clientName":"Sandbox API account for MYNESTHUB","clientCode":"424ac743-5ed8-448d-a2c6-6e86f7f55791","productName":"TazAPI - Tenant Product","requestedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","searchFlagged":false,"quickappApplicantLink":"https://lightning.instascreen.net/orderquickapp/index.taz?x=6535qqg8ej72p7ravv1thnd7qi4mf8grkhm1lvy0wSKkRJMWkdghRMJvJe3i7LppVMDwJkSeYDXZozdJY&y=410943&z=1","createdDate":1616171587000,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","modifiedDate":1616171587116,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent"}


                ============


                {"orderGuid":"2a6738c8-96d2-49d8-8a6b-b172385e9c0b","fileNumber":412962,"orderStatus":"app-pending","orderType":"Employment","orderedDate":1616658846000,"generalReportReference":"Mid-Group","externalIdentifier":"newInentificator1","applicantName":"CLEAN, JOE","clientName":"Sandbox API account for MYNESTHUB","clientCode":"424ac743-5ed8-448d-a2c6-6e86f7f55791","productName":"TazAPI - Tenant Product","requestedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","searchFlagged":false,"quickappApplicantLink":"https://lightning.instascreen.net/orderquickapp/index.taz?x=sgl9jufqssbqmahj060gvd4qruu8k11g2f0pj4W896zzRfD5QZu7CgdeIyslgcGDP0DId10LnupsNxx54&y=412962&z=1","createdDate":1616658846000,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent","modifiedDate":1616658846942,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent"}


                */

        /*

                // Order Results as PDF


                $curl = curl_init();

                curl_setopt_array($curl, array(
                    //CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/{{client-guid}}/orders/{{order-guid}}/resultsPdf',
                    CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/1166b165-f8f5-4718-b9d4-82b04f14f7e6/orders/df29fa2e-7556-4b45-94f5-7c2ed2ee8eec/resultsPdf',
                    //CURLOPT_URL => 'https://api-sandbox.instascreen.net/v1/clients/1166b165-f8f5-4718-b9d4-82b04f14f7e6/orders/2a6738c8-96d2-49d8-8a6b-b172385e9c0b/resultsPdf',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
        /*

        "https://lightning.instascreen.net/send/interchangeview/?a=i014vgnkm8imiqk4nokieglh64tkd1v9t2se5KUcBDbYYI4EP4dHuesLVDGJb7Z39iUATwwSzegEOFvJF&b=2&c=rptview&file=410828&format=pdf"


         * */


    }
}
