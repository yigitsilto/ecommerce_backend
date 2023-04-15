<?php

namespace FleetCart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutParamRequest extends FormRequest
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
            'checkoutForm.GUID' => 'required',
            'checkoutForm.KK_Sahibi' => 'required',
            'checkoutForm.KK_No' => 'required',
            'checkoutForm.KK_SK_Ay' => 'required',
            'checkoutForm.KK_SK_Yil' => 'required',
            'checkoutForm.KK_CVC' => 'required',
            'checkoutForm.KK_Sahibi_GSM' => 'required',
            'checkoutForm.Taksit' => 'required',
            'checkoutForm.Siparis_ID' => 'required|exists:order_snapshot,id',
            'checkoutForm.Siparis_Aciklama' => 'nullable',
            'checkoutForm.SanalPOS_ID' => 'required',
            'checkoutForm.ratio' => 'required',
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['required'],
            'customer_first_name' => ['required'],
            'customer_last_name' => ['required'],
            'address' => ['required', 'exists:addresses,id'],
            'coupon_id' => ['nullable','exists:coupons,id'],
            'free_shipping' => ['required','boolean'],
            'payment_method' => ['required',Rule::in(['bank_transfer', 'credit_cart'])],
            'shipping_method' => ['required_if:free_shipping,1'],
            'coupon' => ['nullable']
        ];
    }
}
