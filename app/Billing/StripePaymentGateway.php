<?php

namespace App\Billing;

use App\Organization;
use Laravel\Cashier\Subscription;
use App\Billing\PaymentFailedException;

class StripePaymentGateway implements PaymentGateway
{
    /**
     * The organization to be charged.
     *
     * @var Organization
     */
    protected $organization;

    /**
     * The org administrator's email address
     *
     * @var string
     */
    protected $email;

    /**
     * The type of subscription to be created
     *
     * @var string
     */
    protected $selectedPlan;

    /**
     * The number of seats to be added to the new subscription
     *
     * @var integer
     */
    protected $seatQuantity = 1;

    /**
     * Select an organization to be subscribed
     *
     * @param Organization $organization
     * @param string $email
     * @return self
     */
    public function subscribe(Organization $organization, $email)
    {
        $this->organization = $organization;
        $this->email = $email;

        return $this;
    }

    /**
     * Select the plan to subscribe to
     *
     * @param string $name
     * @return self
     */
    public function to($name)
    {
        $this->selectedPlan = $this->getPlan($name);

        return $this;
    }

    /**
     * Retrieve a stripe plan id from a plan name
     *
     * @param string $name
     * @return array
     */
    protected function getPlan($name)
    {
        $plans = collect([
            [
                'name' => 'vip',
                'identifier' => 'plan_DdZ8AM9m0OEGAu'
            ],
            [
                'name' => 'free',
                'identifier' => 'plan_DdZ7WJo5Hq1ZMH'
            ],
        ])->keyBy('name');


        if (!$plans->has($name)) {
            throw new PaymentFailedException("Invalid subscription type selected.");
        }

        return $plans->get($name);
    }

    /**
     * Create the subscription
     *
     * @param string $token
     * @return Subscription
     */
    public function charge($token)
    {
        try {
            return $this->organization->newSubscription(
                    $this->selectedPlan['name'],
                    $this->selectedPlan['identifier']
                )->create($token, [
                    'email' => $this->email,
                    'metadata' => [
                        'organization' => $this->organization->name
                    ]
                ]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }
}
