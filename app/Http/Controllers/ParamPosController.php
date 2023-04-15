<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\CheckoutParamRequest;
use FleetCart\Services\CardTypeBinApiService;
use FleetCart\Services\CheckoutService;
use FleetCart\Services\ParamPosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ParamPosController extends Controller
{
    private $paramPosInstallmentService;

    private $cardTypeService;

    private $checkoutService;

    public function __construct(ParamPosService $paramPosInstallmentService, CardTypeBinApiService $cardTypeBinApiService, CheckoutService $checkoutService)
    {
        $this->paramPosInstallmentService = $paramPosInstallmentService;
        $this->cardTypeService = $cardTypeBinApiService;
        $this->checkoutService = $checkoutService;
    }

    public function index()
    {
        return response()->json(['DT_Ozel_Oranlar_SK' => $this->paramPosInstallmentService->getAllInstalments()]);
    }

    /**
     * Kart tipini ve gerekli taksit bilgisini döner
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCardInformations(Request $request)
    {
        return response()->json($this->cardTypeService->getCardInformations($request));

    }

    /**
     * kredi kartı ödemesi alır
     * @param CheckoutParamRequest $request
     * @return JsonResponse
     */
    public function checkout(CheckoutParamRequest $request): JsonResponse
    {

        return $this->paramPosInstallmentService->checkout($request);
    }

    /**
     * Param 3d ödemesinden sonra atılan success url
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function successPayment(Request $request)
    {
        $this->paramPosInstallmentService->successPayment($request);
        return redirect()->to(env('FE_URL') . 'payment/info');
    }

    /**
     * Param 3d ödemesinden sonra atılan fail url
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function errorPayment(Request $request)
    {
        return redirect()->to(env('FE_URL') . 'payment/error');
    }
}
