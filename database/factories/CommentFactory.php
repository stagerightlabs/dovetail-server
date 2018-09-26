<?php

use App\Page;
use App\Organization;
use Faker\Generator as Faker;

$factory->define(App\Comment::class, function (Faker $faker) {
    return [
        'content' => $faker->sentences(3, true),
        'commentor_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'commentable_type' => 'page',
        'commentable_id' => function () {
            return factory(Page::class)->create()->id;
        },
        'edited' => false,
    ];
});
