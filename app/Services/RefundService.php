<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\StoreRefundRequest;

interface RefundService
{

    public function index();

    public function store(StoreRefundRequest $request);

}