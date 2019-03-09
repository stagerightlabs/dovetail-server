<?php

use Illuminate\Support\Str;
use App\Page;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(App\Document::class, function (Faker $faker) {
    $filename = Str::random(16) . '.png';
    $file = UploadedFile::fake()->image($filename)->storePublicly("attachments", 's3');

    return [
        'documentable_type' => 'page',
        'documentable_id' => function () {
            return factory(Page::class)->create()->id;
        },
        'original' => $file,
        'standard' => 'attachments/document-standard.png',
        'thumbnail' => 'attachments/document-thumbnail.png',
        'icon' => 'attachments/document-icon.png',
        'filename' => $filename,
        'mimetype' => 'image/png',
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
