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
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'slug' => $this->slug,
            'price' => $this->price,
            'special_price' => $this->special_price,
            'special_price_type' => $this->special_price_type,
            'special_price_start' => $this->special_price_start,
            'special_price_end' => $this->special_price_end,
            'selling_price' =>$this->selling_price,
            'sku' => $this->sku,
            'manage_stock' => $this->manage_stock,
            'qty' => $this->qty,
            'in_stock' => $this->in_stock,
            'is_active' => $this->is_active,
            'new_from' => $this->new_from,
            'new_to' => $this->new_to,
            'is_popular' => $this->is_popular,
            'base_image' => $this->base_image,
            'formatted_price' => $this->formatted_price,
            'rating_percent' => $this->rating_percent,
            'is_in_stock' => $this->is_in_stock,
            'is_out_of_stock' => $this->is_out_of_stock,
            'has_percentage_special_price' => $this->has_percentage_special_price,
            'special_price_percent' => $this->special_price_percent,
            'normalPrice' => $this->normalPrice,
            'name' => $this->name,
            'options' => $this->whenLoaded('options', function () {
                return $this->options;
            }),
        ];
    }
}
