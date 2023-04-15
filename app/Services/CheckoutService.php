<?php

namespace FleetCart\Services;


use FleetCart\Http\Requests\StoreCheckoutRequest;
use Modules\Core\Http\Requests\Request;

interface CheckoutService {


    public function store(StoreCheckoutRequest $request, $userId = null);

    public function calculateTotalPriceInBasket($userId = null);

    public function createOrderSnapshot(StoreCheckoutRequest $request);

    public function getTotalValuesFromBasket($request, $userId = null);




}
