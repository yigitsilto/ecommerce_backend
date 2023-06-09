<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomePageProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ProductResource
     */
    public function toArray($request)
    {
        return  new ProductResource($this->whenLoaded('product'));

    }
}
