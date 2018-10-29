<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "amount" => $this->getAmount(),
            "description" => $this->getDescription(),
            "timestamp" => $this->getTimestamp()
        );
    }
}
