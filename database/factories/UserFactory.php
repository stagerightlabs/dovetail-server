<?php

use Illuminate\Support\Str;
use App\AccessLevel;
use App\Organization;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

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
        'phone' => $faker->phoneNumber,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'access_level' => AccessLevel::$ORGANIZATION_MEMBER,
        'remember_token' => Str::random(10),
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'email_verified_at' => Carbon::now()->subDays(2),
        'phone_verified_at' => Carbon::now()->subDays(2),
        'title' => null,
        'deleted_at' => null,
    ];
});

$factory->state(App\User::class, 'unverified', [
    'email_verified_at' => null,
    'phone_verified_at' => null,
]);

$factory->state(App\User::class, 'org-member', [
    'access_level' => AccessLevel::$ORGANIZATION_MEMBER,
]);

$factory->state(App\User::class, 'org-admin', [
    'access_level' => AccessLevel::$ORGANIZATION_ADMIN,
]);

$factory->state(App\User::class, 'org-readonly', [
    'access_level' => AccessLevel::$ORGANIZATION_READ_ONLY,
]);

$factory->state(App\User::class, 'deleted', [
    'deleted_at' => Carbon::now()->subDays(2),
]);

$factory->state(App\User::class, 'blocked', [
    'blocked_at' => Carbon::now()->subDays(2),
]);
