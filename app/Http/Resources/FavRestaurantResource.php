<?php

namespace App\Http\Resources;

use App\Models\Neo\Restaurant;
use Illuminate\Http\Resources\Json\JsonResource;

class FavRestaurantResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Restaurant $this */
        return [
            'id'    => $this->getId(),
            'rest_id'  => $this->getRestId(),
            'name' => $this->getName(),
            'image_url' => $this->getImageUrl(),
            'phone' => $this->getPhone(),
            'address' => $this->getAddress(),
            'city' => $this->getCity(),
        ];
    }
}
