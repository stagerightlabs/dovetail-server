<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hashid' => hashid($this->id),
            'original' => $this->original ? $this->url($this->original) : '',
            'large' => $this->large ? $this->url($this->large) : '',
            'small' => $this->small ? $this->url($this->small) : '',
            'mimetype' => $this->mimetype,
            'filename' => $this->filename,
        ];
    }

    /**
     * Generate a signed url for an S3 asset
     *
     * @param string $path
     * @return void
     */
    public function url($path)
    {
        return Storage::disk('s3')->temporaryUrl($path, now()->addHours(24));
    }
}
