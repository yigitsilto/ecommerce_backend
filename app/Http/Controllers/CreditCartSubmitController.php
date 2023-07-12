<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\CheckoutParamRequest;
use FleetCart\Services\CardTypeBinApiService;
use FleetCart\Services\CreditCartSubmitService;
use FleetCart\Services\CreditCartSubmitServiceImpl;
use FleetCart\Services\ParamPosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class CreditCartSubmitController extends Controller
{
    private CreditCartSubmitService $creditCartSubmitService;

    private CardTypeBinApiService $cardTypeService;


    public function __construct(CreditCartSubmitService $paramPosInstallmentService, CardTypeBinApiService $cardTypeBinApiService)
    {
        $this->creditCartSubmitService = $paramPosInstallmentService;
        $this->cardTypeService = $cardTypeBinApiService;
    }

    public function index($type)
    {
        return response()->json(['DT_Ozel_Oranlar_SK' => $this->creditCartSubmitService->getAllInstalments($type)]);
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
    public function checkout(CheckoutParamRequest $request, $type): JsonResponse
    {

        return $this->creditCartSubmitService->checkout($request, $type);
    }

    /**
     * Param 3d ödemesinden sonra atılan success url
     * @param Request $request
     * @return RedirectResponse
     */
    public function successPayment(Request $request, $type)
    {
        $this->creditCartSubmitService->successPayment($request, $type);
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
