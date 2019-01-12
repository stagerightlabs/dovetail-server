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
            'original' => $this->original ? Storage::disk('s3')->url($this->original) : '',
            'large' => $this->large ? Storage::disk('s3')->url($this->large) : '',
            'small' => $this->small ? Storage::disk('s3')->url($this->small) : '',
            'mimetype' => $this->mimetype,
            'filename' => $this->filename,
        ];
    }
}
