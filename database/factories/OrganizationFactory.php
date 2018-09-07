<?php

use App\Organization;
use Faker\Generator as Faker;

$factory->define(Organization::class, function (Faker $faker) {
    $name = $faker->company();

    return [
        'name' => $name,
        'slug' => str_slug($name)
    ];
});
