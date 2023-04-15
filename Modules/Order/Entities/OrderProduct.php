<?php

namespace Modules\Order\Entities;

use Modules\Option\Entities\Option;
use Modules\Option\Entities\OptionValue;
use Modules\Support\Money;
use Modules\Support\Eloquent\Model;
use Modules\Product\Entities\Product;

class OrderProduct extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['product', 'options'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function url()
    {
        return route('products.show', ['slug' => $this->product->slug]);
    }

    public function hasAnyOption()
    {
        return $this->options->isNotEmpty();
    }

    /**
     * Determine if order product has been deleted.
     *
     * @return bool
     */
    public function trashed()
    {
        return $this->product->trashed();
    }

    /**
     * Store order product's options.
     *
     * @param \Illuminate\Database\Eloquent\Collection $options
     * @return void
     */
    public function storeOptions($options)
    {
        $options->each(function ($option) {
            $orderProductOption = $this->options()->create([
                'order_product_id' => $this->id,
                'option_id' => $option->id,
                'value' => $option->isFieldType() ? $option->values->first()->label : null,
            ]);

            $orderProductOption->storeValues($this->product, $option->values);
        });
    }

    public function storeOptionsForApi($options)
    {
        if (count($options) > 0){
            foreach ($options as $option){
                $optionValue = OptionValue::query()
                    ->where('option_id',$option->optionId)
                    ->where('id',$option->valueId)
                    ->first();


                $orderProductOption = $this->options()->create([
                    'order_product_id' => $this->id,
                    'option_id' => $option->optionId,
                    'value' => isset($optionValue) ? $optionValue->label : null,
                ]);

                $values = [
                    [
                        'option_value_id' => $optionValue->id,
                        'price' => isset($optionValue->price) ? $optionValue->price->amount : 0
                    ]

                ];


                $orderProductOption->storeValuesForApi($values);



            }
        }

    }

    public function product()
    {
        return $this->belongsTo(Product::class)
            ->withoutGlobalScope('active')
            ->withTrashed();
    }

    public function options()
    {
        return $this->hasMany(OrderProductOption::class);
    }

    /**
     * Get the order product's name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->product->name;
    }

    /**
     * Get the order product's slug.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->product->slug;
    }

    public function getUnitPriceAttribute($unitPrice)
    {
        return Money::inDefaultCurrency($unitPrice);
    }

    public function getLineTotalAttribute($total)
    {
        return Money::inDefaultCurrency($total);
    }
}
