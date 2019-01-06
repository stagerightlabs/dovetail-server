<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'commentator' => $this->commentator->name,
            'commentator_id' => hashid($this->commentator_id),
            'edited' => $this->edited,
            'created_at' => $this->created_at->toAtomString(),
            'since_created' => $this->created_at->diffForHumans(),
        ];
    }
}
