<?php

use App\Organization;
use Faker\Generator as Faker;

$factory->define(App\Invitation::class, function (Faker $faker) {
    return [
        'email' => $faker->safeEmail(),
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        }
    ];
});
