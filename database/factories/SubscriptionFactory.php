<?php

use Faker\Generator as Faker;
use Laravel\Cashier\Subscription;

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'name' => 'vip',
        'stripe_id' => 'sub_DdZJHY1iHeDHqm',
        'stripe_plan' => 'plan_DdZ8AM9m0OEGAu',
        'quantity' => 1,
        'trial_ends_at' => null,
        'ends_at' => null
    ];
});
