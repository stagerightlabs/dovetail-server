<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'uuid' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
            'read_at' => $this->read_at ? $this->read_at->toAtomString() : null,
            'created_at' => $this->created_at->toAtomString(),
        ];
    }
}
