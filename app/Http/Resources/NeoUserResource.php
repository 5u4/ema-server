<?php

namespace App\Http\Resources;

use App\Models\Neo\User;
use Illuminate\Http\Resources\Json\JsonResource;

class NeoUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var User $this */
        return [
            'id'    => $this->getId(),
            'sqlId' => $this->getSqlId(),
        ];
    }
}
