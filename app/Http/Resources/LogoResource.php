<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class LogoResource extends JsonResource
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
            'hashid' => hashid($this->id) ?? '',
            'original' => $this->original ? Storage::disk('s3')->url($this->original) : '',
            'standard' => $this->standard ? Storage::disk('s3')->url($this->standard) : '',
            'thumbnail' => $this->thumbnail ? Storage::disk('s3')->url($this->thumbnail) : '',
            'icon' => $this->icon ? Storage::disk('s3')->url($this->icon) : '',
        ];
    }
}
