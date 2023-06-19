<?php

namespace Modules\Coupon\Checkers;

use Closure;
use FleetCart\Basket;
use Modules\Coupon\Exceptions\InapplicableCouponException;

class ExcludedProducts
{
    public function handle($coupon, Closure $next)
    {
        $coupon->load('excludeProducts');

        if ($coupon->excludeProducts->isEmpty()) {
            return $next($coupon);
        }

        foreach (Basket::query()
                       ->whereHas('product', function ($q) {
                           $q->where('is_active', 1);
                       })
                       ->where('user_id', auth('api')->user()->id)
                       ->get() as $cartItem) {

            if ($this->inExcludedProducts($coupon, $cartItem)) {
                throw new InapplicableCouponException;
            }
        }

        return $next($coupon);
    }

    private function inExcludedProducts($coupon, $cartItem)
    {
        return $coupon->excludeProducts->contains($cartItem->product);
    }
}
