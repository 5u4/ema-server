<?php

namespace App\Http\Resources;

use App\Models\Sql\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Permission $this */

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}
