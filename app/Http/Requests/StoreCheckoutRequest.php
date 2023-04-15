<?php

namespace FleetCart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Payment\Facades\Gateway;
use Modules\Support\Country;

class StoreCheckoutRequest extends FormRequest
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

    public function rules()
    {
        return [
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['required'],
            'customer_first_name' => ['required'],
            'customer_last_name' => ['required'],
            'address' => ['required', 'exists:addresses,id'],
            'coupon_id' => ['nullable','exists:coupons,id'],
            'free_shipping' => ['required','boolean'],
            //'create_an_account' => 'boolean',
            //'password' => 'required_if:create_an_account,1',
            //'ship_to_different_address' => 'boolean',
            /*
             * 'shipping.first_name' => 'required_if:ship_to_a_different_address,1',
            'shipping.last_name' => 'required_if:ship_to_a_different_address,1',
            'shipping.address_1' => 'required_if:ship_to_a_different_address,1',
            'shipping.city' => 'required_if:ship_to_a_different_address,1',
            'shipping.zip' => 'required_if:ship_to_a_different_address,1',
            'shipping.country' => ['required_if:ship_to_a_different_address,1'],
            'shipping.state' => 'required_if:ship_to_a_different_address,1',
             */
            'payment_method' => ['required',Rule::in(['bank_transfer', 'credit_cart'])],
            'shipping_method' => ['required_if:free_shipping,1'],
            'coupon' => ['nullable']
            //'terms_and_conditions' => 'accepted',
        ];
    }
}
