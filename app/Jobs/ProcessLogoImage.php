<?php

namespace App\Jobs;

use App\Logo;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Intervention\Image\Facades\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessLogoImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The logo to be processed
     *
     * @var Logo
     */
    public $logo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($logo)
    {
        $this->logo = $logo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageContents = Storage::disk('s3')->get($this->logo->original);
        $pathinfo = pathinfo($this->logo->original);

        // Create the 'large' logo
        $image = Image::make($imageContents);
        $image->resize(150, 150, function ($constraint) {
            // $constraint->aspectRatio();
        })->encode();
        $this->logo->large = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($this->logo->large, (string)$image);

        // Create the 'small' logo
        $image = Image::make($imageContents);
        $image->resize(50, 50, function ($constraint) {
            // $constraint->aspectRatio();
        })->encode();
        $this->logo->small = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($this->logo->small, (string)$image);

        // All set
        $this->logo->save();
    }
}
