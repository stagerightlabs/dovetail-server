<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'email' => $this->email,
            'rank' => $this->rank,
            'title' => strval($this->title),
            $this->mergeWhen(auth()->user()->can('edit', request()->organization()), [
                'phone' => $this->phone,
                'email_verified' => !is_null($this->email_verified_at),
                'phone_verified' => !is_null($this->phone_verified_at),
            ]),
            'blocked' => !is_null($this->blocked_at),
            'created_at' => $this->created_at->toAtomString(),
            'deleted_at' => $this->when($this->trashed(), function () {
                return $this->deleted_at ? $this->deleted_at->toAtomString() : null;
            })
        ];
    }
}
