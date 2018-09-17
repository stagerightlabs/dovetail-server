<?php

namespace Tests\Unit;

use App\Logo;
use Tests\TestCase;
use App\Jobs\ProcessLogoImage;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessLogoImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resizes_logos()
    {
        Storage::fake('s3');
        $logo = factory(Logo::class)->create([
            'original' => File::image('logo.png', 600, 800)->store('logos', 's3'),
            'large' => null,
            'small' => null
        ]);

        ProcessLogoImage::dispatch($logo);

        $resizedImage = Storage::disk('s3')->get($logo->fresh()->large);
        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(150, $width);
        $this->assertEquals(150, $height);

        $resizedImage = Storage::disk('s3')->get($logo->fresh()->small);
        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(50, $width);
        $this->assertEquals(50, $height);
    }
}
