<?php

use App\User;
use App\Organization;
use Faker\Generator as Faker;

$factory->define(App\Team::class, function (Faker $faker) {
    return [
        "name" => $faker->words(3, true),
        "organization_id" => function () {
            return factory(Organization::class)->create()->id;
        },
        "created_by" => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
