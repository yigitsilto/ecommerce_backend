<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\StoreCheckoutRequest;
use FleetCart\Services\CheckoutService;
use Illuminate\Http\Request;
use Modules\Coupon\Entities\Coupon;
use Modules\Payment\Facades\Gateway;
use Modules\Setting\Entities\ShippingCompany;

class CheckoutController extends Controller
{

    private $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }


    // TODO sepet kontrolleri ürünler kontrol edilmeli!!!!
    public function store(StoreCheckoutRequest $request)
    {
        /* TODO guest hesabı ile ödeme yapılma eklenecek
         * if (auth()->guest() && $request->create_an_account) {
            $customerService->register($request)->login();
        }
         */
        $order = $this->checkoutService->store($request);

        return response()->json($order);
    }

    public function createOrderSnapshot(StoreCheckoutRequest $request)
    {

        return response()->json(['id' => $this->checkoutService->createOrderSnapshot($request)]);

    }


    public function couponExists(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|exists:coupons,code'
        ]);
        $coupon = Coupon::query()
                        ->where('code', $request->code)
                        ->first();


        $prices = $this->checkoutService->calculateTotalPriceInBasket();
        $totalPrice = $prices['totalPrice'];

        if (isset($coupon->value) && $coupon->value->amount > $totalPrice) {
            return response()->json('Bu kuponu şu an için kullanamazsınız!', 500);
        }


        if (isset($coupon->minimum_spend) && $coupon->minimum_spend->amount > $totalPrice) {
            return response()->json('Bu kupon için minumum sepeti tutarı yeterli değil!', 500);
        }

        if (isset($coupon->maximum_spend) && $coupon->maximum_spend->amount < $totalPrice) {
            return response()->json('Bu kupon için maximum sepeti tutarını aştınız!', 500);
        }


        return response()->json($coupon);
    }

    public function shippingsAndPaymentMethods()
    {


        return response()->json([
                                    'shippingMethods' => ShippingCompany::all(),
                                    'paymentMethods' => Gateway::all(),
                                ]);
    }


}
