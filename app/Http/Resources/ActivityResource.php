<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        #######ASMAA#######
        
        return [
            'name' => $this->name,
            'version' => $this->version,
            //'post_id' => new PostResource($this->post_id),   
        ];
    }
}
