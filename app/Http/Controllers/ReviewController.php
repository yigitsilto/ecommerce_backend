<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Exceptions\BaseException;
use FleetCart\Review;
use Illuminate\Http\Request;
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

        $reivewBefore = $this->checkUserReviewedBefore($request->product_id);


        if(!is_null($reivewBefore)){
            return BaseException::responseServerError('Bu ürünü daha önce değerlendirdiniz');
        }

        if (is_null($orderCheck) || $orderCheck->count() < 1) {
            return BaseException::responseServerError('Değerlendirmek için önce ürünü almanız gerekmektedir');
        }
        $request = $request->merge(['is_approved' => false]);

        $review = Review::create($request->all());
        return response()->json($review);
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

    public function index(Request $request)
    {
        return Review::query()
                     ->where('product_id', $request->product_id)
                     ->where('is_approved', true)
                     ->get();
    }

    private function checkUserReviewedBefore($productId)
    {
        $review = Review::query()
                        ->where('reviewer_id', auth('api')->user()->id)
                        ->where('product_id', $productId)
                        ->first();
        return $review;
    }
}
