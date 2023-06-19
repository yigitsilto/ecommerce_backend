<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\StoreCheckoutRequest;
use FleetCart\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Modules\Coupon\Checkers\ApplicableCategories;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\CouponExists;
use Modules\Coupon\Checkers\ExcludedCategories;
use Modules\Coupon\Checkers\ExcludedProducts;
use Modules\Coupon\Checkers\UsageLimitPerCoupon;
use Modules\Coupon\Checkers\UsageLimitPerCustomer;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Coupon\Entities\Coupon;
use Modules\Payment\Facades\Gateway;
use Modules\Setting\Entities\ShippingCompany;

class CheckoutController extends Controller
{

    private $checkers = [
        CouponExists::class,
        //      TODO  AlreadyApplied::class,
        ValidCoupon::class,
        //        MinimumSpend::class,
        //        MaximumSpend::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
        ApplicableCategories::class,
        ExcludedCategories::class,
        UsageLimitPerCoupon::class,
        UsageLimitPerCustomer::class,
    ];

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
            return response()->json(['message' => 'Bu kuponu şu an için kullanamazsınız!'], 500);
        }


        if (isset($coupon->minimum_spend) && $coupon->minimum_spend->amount > $totalPrice) {
            return response()->json(['message' => 'Bu kupon için minumum sepeti tutarı yeterli değil!'], 500);
        }

        if (isset($coupon->maximum_spend) && $coupon->maximum_spend->amount < $totalPrice) {
            return response()->json(['message' => 'Bu kupon için maximum sepeti tutarını aştınız!'], 500);
        }

        resolve(Pipeline::class)
            ->send($coupon)
            ->through($this->checkers)
            ->then(function ($coupon) {

            });
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
