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
            'standard' => $this->standard ? $this->url($this->standard) : '',
            'thumbnail' => $this->thumbnail ? $this->url($this->thumbnail) : '',
            'icon' => $this->icon ? $this->url($this->icon) : '',
            'mimetype' => $this->mimetype,
            'filename' => $this->filename,
            'uploaded_by' => $this->creator->name,
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
        if (empty($path)) {
            return $path;
        }

        // We are not currently able to run the tests with signed urls
        if (app()->environment('testing')) {
            return Storage::disk('s3')->url($path);
        }

        return Storage::disk('s3')->temporaryUrl($path, now()->addHours(24));
    }
}
