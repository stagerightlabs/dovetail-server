<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class NotebookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Determine owner name
        if ($this->user_id) {
            $ownerName = $this->user->name;
        } elseif ($this->team_id) {
            $ownerName = $this->team->name;
        } else {
            $ownerName = $this->organization->name;
        }

        return [
            'hashid' => hashid($this->id),
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'category_id' => $this->category_id ? hashid($this->category_id) : null,
            'category' => $this->category_id ? $this->category->name : null,
            'owner_name' => $ownerName,
            'comments_enabled' => boolval($this->comments_enabled),
            'current_user_is_following' => $this->hasFollower(auth()->user()),
            'pages' => PageResource::collection($this->whenLoaded('pages')),
        ];
    }
}
