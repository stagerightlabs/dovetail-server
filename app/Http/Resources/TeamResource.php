<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\AccessLevel;

class TeamResource extends JsonResource
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
            'hashid' => $this->hashid,
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'members' => MemberResource::collection($this->whenLoaded('members')),
            'members_count' => $this->when(!is_null($this->members_count), $this->members_count)
        ];
    }
}
