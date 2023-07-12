<?php
namespace FleetCart\Services;

use FleetCart\Http\Requests\CheckoutParamRequest;

interface CreditCartSubmitService
{
    public function getAllInstalments($type);

    public function checkout(CheckoutParamRequest $request, $type);

    public function successPayment($request, $type);
}