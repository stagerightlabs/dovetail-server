<?php

use App\AccessLevel;
use App\Organization;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'access_level' => AccessLevel::$ORGANIZATION_ADMIN,
        'remember_token' => str_random(10),
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        }
    ];
});

$factory->state(App\User::class, 'unverified', [
    'email_verified_at' => null
]);

$factory->state(App\User::class, 'org-user', [
    'access_level' => AccessLevel::$ORGANIZATION_USER,
]);
