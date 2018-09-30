<?php

namespace App\Http\Resources;

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
        return [
            'hashid' => hashid($this->id),
            'name' => $this->name,
            'category_id' => $this->category_id ? hashid($this->category_id) : null,
            'category' => $this->category_id ? $this->category->name : null,
            'comments_enabled' => boolval($this->comments_enabled),
            'current_user_is_following' => $this->hasFollower(auth()->user())
        ];
    }
}
