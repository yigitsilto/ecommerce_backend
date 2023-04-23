<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\StoreRefundRequest;
use FleetCart\Services\RefundService;
use Illuminate\Http\Request;

class RefundController extends Controller
{

    private RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index()
    {
        return response()->json($this->refundService->index());
    }


    public function store(StoreRefundRequest $request)
    {
        return response()->json($this->refundService->store($request));
    }


}
