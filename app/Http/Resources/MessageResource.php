<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

//    public function toArray($request)
//    {
//
//        return [
//            "id" => $this->id,
//            "user_id" => $this->user_id,
//            "message_id" => $this->message_id,
//            "conversation_id" => $this->conversation_id,
//            "from" => $this->from,
//            "to" => $this->to,
//            "body" => $this->body,
//            "fromMe" => $this->fromMe,
//            "type" => $this->type,
//            "created_at" => $this->created_at,
//            "updated_at" => $this->updated_at
//        ];
//    }

    public static $wrap = 'message';

}
