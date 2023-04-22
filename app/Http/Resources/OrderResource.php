<?php

namespace FleetCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'billing_first_name' => $this->billing_first_name,
            'billing_last_name' => $this->billing_last_name,
            'billing_address_1' => $this->billing_address_1,
            'billing_address_2' => $this->billing_address_2,
            'billing_city' => $this->billing_city,
            'billing_state' => $this->billing_state,
            'billing_zip' => $this->billing_zip,
            'billing_country' => $this->billing_country,
            'shipping_first_name' => $this->shipping_first_name,
            'shipping_last_name' => $this->shipping_last_name,
            'shipping_address_1' => $this->shipping_address_1,
            'shipping_address_2' => $this->shipping_address_2,
            'shipping_city' => $this->shipping_city,
            'shipping_state' => $this->shipping_state,
            'shipping_zip' => $this->shipping_zip,
            'shipping_country' => $this->shipping_country,
            'subtotal' => $this->subtotal,
            'shipping_method' => $this->shipping_method,
            'shipping_cost' => $this->shipping_cost,
            'coupon_id' => $this->coupon_id,
            'discount' => $this->discount,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'currency' => $this->currency,
            'currency_rate' => $this->currency_rate,
            'locale' => $this->locale,
            'status' => $this->status,
            'note' => $this->note,
            'installment' => $this->installment,
            'totalWithCommission' => $this->totalWithCommission,
            'products' =>OrderProductsResource::collection($this->products),

        ];
    }
}
