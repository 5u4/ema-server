<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            "id" => $this->getMovieId(),
            "name" => $this->getMovieName(),
            "posterURL" => $this->getPosterURL(),
        );
    }
}
