<?php

use App\Organization;
use Faker\Generator as Faker;

$factory->define(App\Logo::class, function (Faker $faker) {
    return [
        'owner_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'owner_type' => 'organization',
        'original' => 'path/to/original',
        'large' => 'path/to/large',
        'small' => 'path/to/small',
        'filename' => 'filename.png'
    ];
});
