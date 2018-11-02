<?php

namespace App\Http\Resources;

use App\Models\Sql\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lastLogin = is_null($this->last_login) ? null : $this->last_login->timestamp;
        $createdAt = is_null($this->{USER::CREATED_AT}) ? null : $this->{USER::CREATED_AT}->timestamp;
        $updatedAt = is_null($this->{USER::UPDATED_AT}) ? null : $this->{USER::UPDATED_AT}->timestamp;

        return [
            'id'        => $this->id,
            'username'  => $this->username,
            'email'     => $this->email,
            'lastLogin' => $lastLogin,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];
    }
}
