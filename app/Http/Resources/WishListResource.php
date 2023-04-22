<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WishListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          'id' => $this->id,
          'product_id' => $this->product_id,
          'user_id' => $this->user_id,
          'product' => new ProductResource($this->product),
          'user' => $this->whenLoaded('user',function(){
              $this->user;
          }),
        ];
    }
}
