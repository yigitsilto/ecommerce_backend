<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\CheckoutParamRequest;
use FleetCart\Services\CardTypeBinApiService;
use FleetCart\Services\ParamPosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ParamPosController extends Controller
{
    private ParamPosService $paramPosInstallmentService;

    private CardTypeBinApiService $cardTypeService;


    public function __construct(ParamPosService $paramPosInstallmentService, CardTypeBinApiService $cardTypeBinApiService)
    {
        $this->paramPosInstallmentService = $paramPosInstallmentService;
        $this->cardTypeService = $cardTypeBinApiService;
    }

    public function index()
    {
        return response()->json(['DT_Ozel_Oranlar_SK' => $this->paramPosInstallmentService->getAllInstalments()]);
    }

    /**
     * Kart tipini ve gerekli taksit bilgisini döner
     * @param Request $request
     * @return JsonResponse
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
     * @return RedirectResponse
     */
    public function successPayment(Request $request)
    {
        $this->paramPosInstallmentService->successPayment($request);
        return redirect()->to(env('FE_URL') . 'payment/info');
    }

    /**
     * Param 3d ödemesinden sonra atılan fail url
     * @param Request $request
     * @return RedirectResponse
     */
    public function errorPayment(Request $request)
    {
        return redirect()->to(env('FE_URL') . 'payment/error');
    }
}
