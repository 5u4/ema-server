<?php

namespace App\Http\Resources;

use App\Models\Neo\Log;
use App\Models\Sql\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LogFlowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var User $from */
        $from = User::find($this['from']->getSqlId());
        /** @var Log $log */
        $log = $this['log'];
        /** @var User $to */
        $to = $this['to'] ? User::find($this['to']->get('sqlId')) : null;

        $formattedTo = [];

        if ($to) {
            $formattedTo['username'] = $to->username;
            $formattedTo['email']    = $to->email;
        }

        return [
            'key'      => $log->getId(),
            'from'     => [
                'username' => $from->username,
                'email'    => $from->email,
            ],
            'activity' => $log->getActivity(),
            'to'       => $formattedTo,
            'at'       => $log->getTimestamp(),
        ];
    }
}
