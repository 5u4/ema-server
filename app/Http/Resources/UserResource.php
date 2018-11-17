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
        $lastLogin = is_null($this->last_login) ? null : strtotime($this->last_login);
        $createdAt = is_null($this->{USER::CREATED_AT}) ? null : strtotime($this->{USER::CREATED_AT});
        $updatedAt = is_null($this->{USER::UPDATED_AT}) ? null : strtotime($this->{USER::UPDATED_AT});

        return [
            'id'        => $this->id,
            'username'  => $this->username,
            'email'     => $this->email,
            'avatarUrl' => action('AvatarController@show', ['user' => $this->id]),
            'lastLogin' => $lastLogin,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];
    }
}
