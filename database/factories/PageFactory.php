<?php

use App\User;
use App\Notebook;
use Faker\Generator as Faker;

$factory->define(App\Page::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph(),
        'notebook_id' => function () {
            return factory(Notebook::class)->create()->id;
        },
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'created_at' => 1
    ];
});
