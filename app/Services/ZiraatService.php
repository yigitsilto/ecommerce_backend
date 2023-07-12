<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\CheckoutParamRequest;

interface ZiraatService
{
    public function getAllInstalments();

    public function checkout(CheckoutParamRequest $request);

    public function successPayment($request);

}