<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Option\Entities\OptionValue;
use Modules\Product\Entities\Product;
use Modules\Support\Money;

class Basket extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['total','totalPriceNotFormatted'];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    // set sum of price with quantity of product
    public function getTotalAttribute(){
            $normalPrice = $this->product->normalPrice ?? 0;
            if (isset($this->product->special_price)){
                $normalPrice = $this->product->special_price->amount;
            }


        $options = json_decode($this->options);

        if (count($options) > 0){
            foreach ($options as $option){
                $optionValue = OptionValue::query()
                    ->where('option_id',$option->optionId)
                    ->where('id',$option->valueId)
                    ->first();

                $normalPrice+= isset($optionValue->price) ? $optionValue->price->amount : 0;
            }
        }


            $total =  $normalPrice * $this->quantity;
            return Money::inDefaultCurrency($total);


    }

    // set sum of price with quantity of product
    public function getTotalPriceNotFormattedAttribute(){
        $normalPrice = $this->product->normalPrice ?? 0;
        if (isset($this->product->special_price)){
            $normalPrice = $this->product->special_price->amount;
        }

        $options = json_decode($this->options);

        if (count($options) > 0){
            foreach ($options as $option){
                $optionValue = OptionValue::query()
                    ->where('option_id',$option->optionId)
                    ->where('id',$option->valueId)
                    ->first();

                $normalPrice+= isset($optionValue->price) ? $optionValue->price->amount : 0;
            }
        }

        $total =  $normalPrice * $this->quantity;
        return $total;


    }
}
