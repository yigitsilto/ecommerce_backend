<?php

namespace Modules\Coupon\Checkers;

use Closure;
use FleetCart\Basket;
use Modules\Coupon\Exceptions\InapplicableCouponException;

class ApplicableProducts
{
    public function handle($coupon, Closure $next)
    {
        $coupon->load('products');

        if ($coupon->products->isEmpty()) {
            return $next($coupon);
        }


        $cartItems = Basket::query()
                           ->where('user_id', auth('api')->user()->id)
                           ->get()
                           ->filter(function ($cartItem) use ($coupon) {
                               return $coupon->products->contains($cartItem->product);
                           });

        if ($cartItems->isEmpty()) {
            throw new InapplicableCouponException;
        }

        return $next($coupon);
    }
}
