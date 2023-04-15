<?php

namespace FleetCart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Entities\Product;

class StoreBasketRequestAll extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $product = Product::with('options')
            ->select('id', 'manage_stock', 'qty')
            ->findOrFail($this->product_id);

        return array_merge([
            'quantity' => ['required', 'numeric', $this->maxQty($product)],
        ]);
            //$this->getOptionsRules($product->options));
    }

    /**
     * Get the max qty rule for the given product.
     *
     * @param \Modules\Product\Entities\Product $product
     * @return string|null
     */
    private function maxQty($product)
    {
        $qty = 10;
        if ($product->manage_stock) {
            if ($product->qty < 10){
                $qty = $product->qty;
            }
        }
        return "max:{$qty}";
    }

    /**
     * Get rules for the given options.
     *
     * @param \Illuminate\Database\Eloquent\Collection $options
     * @return array
     */
    private function getOptionsRules($options)
    {
        return $options->flatMap(function ($option) {
            return ["options.{$option->id}" => $this->getOptionRules($option)];
        })->all();
    }

    /**
     * Get rules for the given option.
     *
     * @param \Modules\Option\Entities\Option $option
     * @return array
     */
    private function getOptionRules($option)
    {
        $rules = [];

        if ($option->is_required) {
            $rules[] = 'required';
        }

        if (in_array($option->type, ['dropdown', 'radio'])) {
            $rules[] = Rule::in($option->values->map->id->all());
        }

        return $rules;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge(
            $this->all(),
            [
                'options' => array_filter($this->options ?? []),
            ]
        );
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge([
            'options.*.required' => trans('cart::validation.this_field_is_required'),
            'options.*.in' => trans('cart::validation.the_selected_option_is_invalid'),
        ], parent::messages());
    }
}
