<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewSource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return Array(
            "id" => $this->getId(),
            "reviewTitle" => $this->getReviewTitle(),
            "reviewContent" => $this->getReviewContent(),
            "userId" => $this->getUserId(),
        );
    }
}
