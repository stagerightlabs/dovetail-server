<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'user_id' => $this->causer->hashid,
            'user_name' => $this->causer->name,
            'description' => $this->description,
            'created_at' => $this->created_at->toAtomString(),
            'since_created' => $this->created_at->diffForHumans(),
        ];
    }
}
