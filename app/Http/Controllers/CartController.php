<?php

namespace FleetCart\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Modules\Cart\Http\Requests\StoreCartItemRequest;
use Modules\Shipping\Facades\ShippingMethod;

class CartController extends Controller
{
    public function store(StoreCartItemRequest  $request){
        Cart::store($request->product_id, $request->qty, $request->options ?? []);
        return Cart::instance();
    }

    public function shippingList(){

        return Cart::items();
    }
}
