<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'=>$this->id,
          'slug' => $this->slug,
            'price'=>$this->price,
            'selling_price'=>$this->selling_price,
            'special_price_type'=>$this->special_price_type,
//            'brand' => $this->whenLoaded('brand'),

        ];
    }
}
