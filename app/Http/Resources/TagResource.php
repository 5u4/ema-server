<?php

namespace App\Http\Resources;

use App\Models\Neo\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Tag $this */
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'color' => $this->getColor(),
        ];
    }
}
