<?php

namespace App\Billing;

use App\Organization;
use Laravel\Cashier\Subscription;

interface PaymentGateway
{
    /**
     * Select an organization to be subscribed
     *
     * @param Organization $organization
     * @param string $email
     * @return self
     */
    public function subscribe(Organization $organization, $email);

    /**
     * Select the plan to subscribe to
     *
     * @param string $name
     * @return self
     */
    public function to($name);

    /**
     * Create the subscription
     *
     * @param string $token
     * @return Subscription
     */
    public function charge($token);
}
