<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
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
            'email' => $this->email,
            'revoked_at' => $this->revoked_at ? $this->revoked_at->toAtomString() : null,
            'revoked_by' => $this->revoked_by ? $this->revoker->name : null,
            'completed_at' => $this->completed_at ? $this->completed_at->toAtomString() : null,
            'created_at' => $this->created_at->toAtomString(),
        ];
    }
}
