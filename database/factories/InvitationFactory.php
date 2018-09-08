<?php

use App\User;
use App\Organization;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Invitation::class, function (Faker $faker) {
    return [
        'email' => $faker->safeEmail(),
        'revoked_at' => null,
        'revoked_by' => null,
        'completed_at' => null,
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
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

$factory->state(App\Invitation::class, 'completed', function ($faker) {
    return [
        'completed_at' => Carbon::now()->subDays(2),
    ];
});
