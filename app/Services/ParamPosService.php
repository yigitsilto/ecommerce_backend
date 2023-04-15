<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\CheckoutParamRequest;

interface ParamPosService
{
    public function getAllInstalments();

    public function checkout(CheckoutParamRequest $request);

    public function successPayment($request);
}
