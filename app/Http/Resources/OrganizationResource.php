<?php

namespace App\Http\Resources;

use App\Http\Resources\LogoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => new LogoResource($this->logo),
            'config' => $this->configuration,
        ];
    }
}
