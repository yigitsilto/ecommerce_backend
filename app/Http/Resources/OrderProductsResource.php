<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'unit_price' => $this->unit_price,
            'qty' => $this->qty,
            'line_total' => $this->line_total,
            'created_at' => $this->created_at,
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'options' => $this->whenLoaded('options', function () {
                return $this->options;
            }),
        ];
    }
}
