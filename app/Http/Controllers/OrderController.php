<?php

namespace FleetCart\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Order\Entities\Order;

class OrderController extends Controller
{
    public function index(){
        $orders = Order::query()
            ->where('customer_email',auth('api')->user()->email)
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json($orders);

    }

    public function findById($id){
        $order = Order::query()
            ->with('products')
            ->whereHas('products')
            ->findOrFail($id);

        return response()->json($order);

    }
}
