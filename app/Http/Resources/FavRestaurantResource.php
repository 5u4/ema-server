<?php

namespace App\Http\Resources;

use App\Models\Neo\Restaurant;
use Illuminate\Http\Resources\Json\JsonResource;

class FavRestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Restaurant $this */
        return [
            'id'    => $this->getId(),
            'rest_id'  => $this->getRestId(),
        ];
    }
}
