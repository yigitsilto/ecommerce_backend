<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasketResource extends JsonResource
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
            'id' =>$this->id,
            'product_id' =>$this->product_id,
            'user_id' =>$this->user_id,
            'quantity' =>$this->quantity,
            'options' =>$this->options ? json_decode($this->options) : null,
            'created_at' =>$this->created_at,
            'updated_at' =>$this->updated_at,
            'price' =>$this->price,
            'total' =>$this->total,
            'totalPriceNotFormatted' =>$this->totalPriceNotFormatted,
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            })
        ];
    }
}
