<?php

use App\Page;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(App\Document::class, function (Faker $faker) {
    $filename = str_random(16) . '.png';
    $file = UploadedFile::fake()->image($filename)->storePublicly("attachments", 's3');
    // dd($file, $filename);

    return [
        'documentable_type' => 'page',
        'documentable_id' => function () {
            return factory(Page::class)->create()->id;
        },
        'original' => $file,
        'large' => 'attachments/document-large.png',
        'small' => 'attachments/document-small.png',
        'filename' => $filename,
        'mimetype' => 'image/png',
    ];
});
