<?php

namespace App\Http\Resources;

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
            'hashid' => hashid($this->id),
            'original' => strval($this->original),
            'large' => strval($this->large),
            'small' => strval($this->small),
        ];
    }
}
