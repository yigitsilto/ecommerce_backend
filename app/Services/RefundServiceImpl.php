<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\StoreRefundRequest;
use FleetCart\Refund;

class RefundServiceImpl implements RefundService
{
    public function index()
    {
        return Refund::query()
            ->with(['product', 'order'])
                     ->where('user_id', auth('api')
                         ->user()
                         ->getAuthIdentifier())
                     ->orderBy("created_at", "desc")
                     ->paginate(20);
    }

    public function store(StoreRefundRequest $request)
    {
        return Refund::query()
                     ->firstOrCreate([
                                         'product_id' => $request->validated()['product_id'],
                                         'order_id' => $request->validated()['order_id'],
                                         'user_id' => auth('api')
                                             ->user()
                                             ->getAuthIdentifier(),
                                     ], [
                                         'reason' => $request->validated()['reason'],
                                         'order_id' => $request->validated()['order_id'],
                                         'product_id' => $request->validated()['product_id'],
                                         'user_id' => auth('api')
                                             ->user()
                                             ->getAuthIdentifier(),
                                     ]);

    }


}