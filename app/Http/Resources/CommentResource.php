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
            'commentor' => $this->commentor->name,
            'commentor_id' => hashid($this->commentor_id),
            'edited' => $this->edited,
            'created_at' => $this->created_at->toAtomString(),
        ];
    }
}
