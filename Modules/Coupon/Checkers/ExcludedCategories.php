<?php

namespace Modules\Coupon\Checkers;

use Closure;
use FleetCart\Basket;
use Modules\Coupon\Exceptions\InapplicableCouponException;

class ExcludedCategories
{
    public function handle($coupon, Closure $next)
    {
        $coupon->load('excludeCategories');

        if ($coupon->excludeCategories->isEmpty()) {
            return $next($coupon);
        }

        foreach (Basket::query()
                       ->where('user_id', auth('api')->user()->id)
                       ->get() as $cartItem) {
            if ($this->inExcludedCategories($coupon, $cartItem)) {
                throw new InapplicableCouponException;
            }
        }

        return $next($coupon);
    }

    private function inExcludedCategories($coupon, $cartItem)
    {
        return $coupon->excludeCategories->intersect($cartItem->product->categories)
                                         ->isNotEmpty();
    }
}
