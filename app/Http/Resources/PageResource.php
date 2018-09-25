<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'notebook_id' => hashid($this->notebook_id),
            'content' => $this->content,
            'sort_order' => $this->sort_order,
        ];
    }
}
