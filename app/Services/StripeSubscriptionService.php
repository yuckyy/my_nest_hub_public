<?php
namespace App\Services;

use App\Services\Contracts\StripeSubscriptionServiceInterface;

class StripeSubscriptionService implements StripeSubscriptionServiceInterface
{
    public function __construct()
    {
        $this->mode = config('services.stripe.mode');
        $this->stripe_api_key = config('services.stripe.'.$this->mode.'.api_key');
        $this->stripe_client_id = config('services.stripe.'.$this->mode.'.client_id');

        $this->stripe = new \Stripe\StripeClient($this->stripe_api_key);
    }

    /**
     * Create Product
     *
     * @return Product
     */
    public function createProduct($name)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->products->create([
            'name' => $name
        ]);
    }

    /**
     * Update Product
     *
     * @return Product
     */
    public function updateProduct($id,$name)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->products->update(
            $id,
            ['name' => $name]
        );
    }

    /**
     * Create Subscription Plan
     *
     * @return Plan
     */
    public function createPlan($data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->plans->create($data);
    }

    public function updatePlan($id, $data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->plans->update($id, $data);
    }

    /**
     * Create Subscription
     *
     * @return Subscription
     */
    public function createSubscription($data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->subscriptions->create($data);
    }

    /**
     * Retrive Subscription
     *
     * @return Subscription
     */
    public function retrieveSubscription($sub_id)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->subscriptions->retrieve($sub_id,[]);
    }

    public function cancelSubscription($sub_id)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->subscriptions->cancel($sub_id,[]);
    }

    public function createCoupon($data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->coupons->create($data);
    }

    public function updateCoupon($id,$data)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->coupons->update($id,$data);
    }

    public function deleteCoupon($id)
    {
        $curl = new \Stripe\HttpClient\CurlClient();
        $curl->setEnablePersistentConnections(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        return $this->stripe->coupons->delete($id);
    }
}
