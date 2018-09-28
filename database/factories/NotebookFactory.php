<?php

use App\User;
use App\Organization;
use Faker\Generator as Faker;

$factory->define(App\Notebook::class, function (Faker $faker) {
    return [
        'name' => $faker->words(3, true),
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'comments_enabled' => true
    ];
});
