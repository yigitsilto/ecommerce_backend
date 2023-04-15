<?php

namespace FleetCart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryProductListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order' => 'sometimes|in:orderByName,orderByNameAsc,orderByPrice,orderByPriceAsc',
            'brands' => 'sometimes'
        ];
    }

    protected function passedValidation(){
        if ($this->has('brands')) {
            $this->merge([
                'brands' => $this->brands ? explode(',', $this->brands) : []
            ]);
        }

    }

}
