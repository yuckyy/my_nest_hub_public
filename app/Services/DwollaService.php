<?php

namespace App\Services;

use App\Services\Contracts\DwollaServiceInterface;
use Illuminate\Support\Facades\Session;

class DwollaService implements DwollaServiceInterface
{


    public static function getClient() {
        //APP_ENV=staging, local, production
        //if (env('APP_ENV') == 'local'){}

        $api_key = env('DWOLLA_API_KEY');
        $api_secret = env('DWOLLA_API_SECRET');
        $api_url = env('DWOLLA_API_URL');

        \DwollaSwagger\Configuration::$username = $api_key;
        \DwollaSwagger\Configuration::$password = $api_secret;
        $apiClient = new \DwollaSwagger\ApiClient($api_url);
        $tokensApi = new \DwollaSwagger\TokensApi($apiClient);
        $appToken = $tokensApi->token();
        \DwollaSwagger\Configuration::$access_token = $appToken->access_token;

        return $apiClient;
    }

    public static function updateWebhook($apiClient) {
        $webhookNewUrl = env('DWOLLA_WEBHOOK_URL');

        $webhookApi = new \DwollaSwagger\WebhooksubscriptionsApi($apiClient);
        $retrieved = $webhookApi->_list();
        $subskriptionsArray = reset($retrieved->_embedded);
        if(count($subskriptionsArray) > 1){
            //Delete all subscriptions
            foreach($subskriptionsArray as $subscription){
                $webhookHref = $subscription->_links->self->href;
                $webhookApi->deleteById($webhookHref);
            }
        }
        //Check if there is correct subscription
        $subscriptionFound = false;
        foreach($subskriptionsArray as $subscription){
            $webhookUrl = $subscription->url;
            if($webhookUrl == $webhookNewUrl){
                $subscriptionFound = true;
                break;
            }
            $webhookHref = $subscription->_links->self->href;
            $webhookApi->deleteById($webhookHref);
        }

        if(true || !$subscriptionFound){
            //Create new webhook subscription
            $webhookApi = new \DwollaSwagger\WebhooksubscriptionsApi($apiClient);
            /** @noinspection PhpParamsInspection */
            $subscription = $webhookApi->create([
                'url' => $webhookNewUrl,
                'secret' => 'mysecret123',
            ]);
            //echo "<br>=======================<br>";
            //var_dump($subscription);
            //echo "<br>=======================<br>";
        }
    }

    public static function getClassifications(){
        if (Session::has('classifications')){
            $classifications = Session::get('classifications');
        } else {
            try {
                $apiClient = DwollaService::getClient();
                DwollaService::updateWebhook($apiClient);

                $businessClassificationsApi = new \DwollaSwagger\BusinessclassificationsApi($apiClient);
                $busClassifications = $businessClassificationsApi->_list();
                $classifications = $busClassifications->_embedded->{'business-classifications'};
            } catch (\DwollaSwagger\ApiException $e) {
                \Log::info("Dwolla Error. Get API, Get Business Classifications." . $e->getResponseBody());
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
                return $message;
            }
            Session::put('classifications', $classifications);
        }
        /*
        foreach($classifications as $class1){
            \Log::info("classifications:".print_r($class1->_embedded->{'industry-classifications'},true));
            \Log::info("==================");
        }
        */
        //return "Test error message";
        return $classifications;
    }
}
