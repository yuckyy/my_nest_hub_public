<?php
namespace App\Services;

use App\Services\Contracts\StripeServiceInterface;

class StripeService implements StripeServiceInterface
{
    public function __construct()
    {
        $this->mode = config('services.stripe.mode');
        $this->stripe_api_key = config('services.stripe.'.$this->mode.'.api_key');
        $this->stripe_client_id = config('services.stripe.'.$this->mode.'.client_id');

        $this->stripe = new \Stripe\StripeClient($this->stripe_api_key);
    }

    /**
     * Create new Token
     *
     * @return Token
     */
    public function createToken($type,$data) //$type = "card" or "bank_account"
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        if ($type == 'bank_account') {
            $data['country'] = 'US';
            $data['currency'] = 'usd';
            $data['account_holder_type'] = 'individual';
        }

        return $this->stripe->tokens->create([$type => $data]);
    }

    /**
     * Add new Customer to Stripe
     *
     * @return Customer
     */
    public function createCustomer($email)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->customers->create([
            "email" => $email
        ]);
    }

    /**
     * Retrive a Customer
     *
     * @return Customer
     */
    public function retriveCustomer($cid)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->customers->retrieve($cid);
    }

    /**
     * Create Source (bank account or credit card)
     *
     */
    public function createSource($cid,$token_id)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->customers->createSource(
            $cid,
            ['source' => $token_id]
        );
    }

    /**
     * Verify Source (bank account)
     *
     */
    public function verifySource($cid,$sid)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->customers->verifySource(
            $cid,$sid,
            ['amounts' => [32, 45]] // sandbox only
        );
    }

    /**
     * Remove Source
     *
     */
    public function deleteSource($cid,$sid)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->customers->deleteSource($cid,$sid,[]);
    }

    /**
     * Url to Connect Stripe account
     *
     */
    public function connectURL($stripe_id)
    {
        $connectURL = 'https://connect.stripe.com/oauth/authorize?merchant='.$stripe_id.'&response_type=code&client_id='.$this->stripe_client_id.'&scope=read_write';

        return $connectURL;
    }

    /**
     * Connect Stripe account
     *
     */
    public function connectAccount($code)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        \Stripe\Stripe::setApiKey($this->stripe_api_key);

        $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        // Access the connected account id in the response
        return $response;
    }

    /**
     * Pay with Stripe
     *
     * @return Charge
     */
    public function createCharge($data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->charges->create($data);
    }
}
