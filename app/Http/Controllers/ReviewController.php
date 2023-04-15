<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\Order;

class ReviewController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'reviewer_id' => 'required',
            'reviewer_name' => 'required',
            'product_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        $orderCheck = $this->checkUserBoughtProduct($request->product_id);

        if (is_null($orderCheck) || $orderCheck->count() < 1){
            return response()->json(['error' => 'Değerlendirmek için önce ürünü almanız gerekmektedir'],406);
        }
        $request = $request->merge(['is_approved' => false]);

        $review = Review::create($request->all());
        return response()->json($review);
    }

    public function index(Request $request)
    {
        return Review::query()->where('product_id', $request->product_id)->where('is_approved',true)->get();
    }

    private function checkUserBoughtProduct($productId)
    {

        $order = Order::query()
            ->with([
                'products' => function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                }
            ])
            ->where('customer_email', auth('api')->user()->email)
            ->whereHas('products', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->first();



        return $order;


    }
}
