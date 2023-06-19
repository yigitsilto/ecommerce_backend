<?php

namespace Modules\Coupon\Checkers;

use Closure;
use FleetCart\Basket;
use Modules\Coupon\Exceptions\InapplicableCouponException;

class ApplicableCategories
{
    public function handle($coupon, Closure $next)
    {
        $coupon->load('categories');

        if ($coupon->categories->isEmpty()) {
            return $next($coupon);
        }

        $cartItems = Basket::query()
                           ->whereHas('product', function ($q) {
                               $q->where('is_active', 1);
                           })
                           ->where('user_id', auth('api')->user()->id)
                           ->get()
                           ->filter(function ($cartItem) use ($coupon) {
                               return $coupon->categories->intersect($cartItem->product->categories)
                                                         ->isNotEmpty();
                           });

        if ($cartItems->isEmpty()) {
            throw new InapplicableCouponException;
        }

        return $next($coupon);
    }
}
