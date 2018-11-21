<?php

namespace App\Http\Resources;

use App\Models\Sql\User;
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
        $userId = $this->getUserId();
        $userName = User::find($userId)->username;

        return Array(
            "id" => $this->getId(),
            "reviewTitle" => $this->getReviewTitle(),
            "reviewContent" => $this->getReviewContent(),
            "username" => $userName,
            "rate" => $this->getRate(),
        );
    }
}
