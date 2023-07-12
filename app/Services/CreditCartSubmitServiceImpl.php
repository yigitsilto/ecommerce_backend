<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\CheckoutParamRequest;

class CreditCartSubmitServiceImpl implements CreditCartSubmitService
{

    private ParamPosService $paramPosService;

    public function __construct(ParamPosService $paramPosService)
    {
        $this->paramPosService = $paramPosService;
    }


    public function getAllInstalments($type)
    {
        if ($type == "param"){
            return $this->paramPosService->getAllInstalments();
        }
    }

    public function checkout(CheckoutParamRequest $request, $type)
    {
        if ($type == "param"){
            return $this->paramPosService->checkout($request);
        }
    }

    public function successPayment($request, $type)
    {
        if ($type == "param"){
            return $this->paramPosService->successPayment($request);
        }
    }
}