<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiningResource extends JsonResource
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
            'id'  => $this['id'],
            'name'        => isset($this['name']) ? $this['name'] : "N/A",
            'image_url'        => isset($this['image_url']) ? $this['image_url'] : null,
            'url'        => isset($this['url']) ? $this['url'] : null,
            'price'        => isset($this['price']) ? $this['price'] : "N/A",
            'city'  =>isset($this['location']['city']) ?$this['location']['city'] : "N/A",
            'address'=> isset($this['location']['address1']) ? $this['location']['address1'] : "N/A",
            'rating'     =>isset($this['rating']) ? $this['rating'] : "N/A",
            'phone'     => isset($this['phone']) ? $this['phone'] : "N/A",
            "review_count"=>isset($this['review_count']) ? $this['review_count'] : "N/A",

        ];
    }
}
