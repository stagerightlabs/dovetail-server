<?php

use App\User;
use App\Organization;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Invitation::class, function (Faker $faker) {
    return [
        'email' => $faker->safeEmail(),
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        }
    ];
});

$factory->state(App\Invitation::class, 'revoked', function ($faker) {
    return [
        'revoked_at' => Carbon::now()->subDays(2),
        'revoked_by' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
