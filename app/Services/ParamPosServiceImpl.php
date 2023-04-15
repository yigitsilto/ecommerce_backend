<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\CheckoutParamRequest;
use FleetCart\Http\Requests\StoreCheckoutRequest;
use FleetCart\OrderSnaphot;
use FleetCart\ParamConnectionHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Param apisini kullanarak ödeme işleriyle alakalı işlemleri yapan servis
 */
class ParamPosServiceImpl implements ParamPosService
{

    private $connect;
    private $globallyUniqueIdentifier;
    private $defaulSendObjects;
    private $checkoutService;

    public function __construct(ParamConnectionHelper $paramConnectionHelper, CheckoutService $checkoutService)
    {
        $saleObjloballyUniqueIdentifier = env('GUID');
        if ($paramConnectionHelper->getConnection() == null) {
            $paramConnectionHelper->setConnection();
        }
        $this->connect = $paramConnectionHelper->getConnection();
        $this->checkoutService = $checkoutService;

    }

    /**
     * Bütün bankaların taksit oranlarını döner
     * @return false|\SimpleXMLElement
     */
    public function getAllInstalments()
    {

        $installmentsObj = new \stdClass();
        $installmentsObj->G = new \stdClass();
        $installmentsObj->G->CLIENT_CODE = env('CLIENT_CODE');
        $installmentsObj->G->CLIENT_USERNAME = env('CLIENT_USERNAME');
        $installmentsObj->G->CLIENT_PASSWORD = env('CLIENT_PASSWORD');
        $installmentsObj->GUID = env('GUID');


        $specialRatioConnection = $this->connect->TP_Ozel_Oran_SK_Liste($installmentsObj); // soap dan gelen bir fonskiyon kredi kart taksit oranlarını listeler


        $resultFromConnection = $specialRatioConnection->TP_Ozel_Oran_SK_ListeResult;


        $DTInfo = $resultFromConnection->{'DT_Bilgi'};


        $finalResult = $resultFromConnection->{'Sonuc'};


        $finalResultString = $resultFromConnection->{'Sonuc_Str'};


        $xmlOne = $DTInfo->{'any'};

        $xmlStringOption = <<<XML
<?xml version='1.0' standalone='yes'?>
<root>
{$xmlOne}
</root>
XML;

        $xmlString = str_replace(array(
                                     "diffgr:",
                                     "msdata:"
                                 ), '', $xmlStringOption);


        $loadXml = simplexml_load_string($xmlString);


        $ratioListXml = $loadXml->diffgram->NewDataSet;


        return $ratioListXml;
    }

    /**
     * Ödeme işlemini yapar
     * @param CheckoutParamRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(CheckoutParamRequest $request)
    {

        $checkoutRequest = $request->validated()['checkoutForm'];

        $priceInformations = $this->checkoutService->getTotalValuesFromBasket($request);

        $totalPrice = $this->tlFormat($priceInformations['total']);
        $totalPriceWithComission = $totalPrice;
        $totalPriceWithComission = $priceInformations['total'] + (($priceInformations['total'] *
                    $checkoutRequest['ratio']) /
                100);

        $totalPriceWithComission = round($totalPriceWithComission, 2, PHP_ROUND_HALF_DOWN);

        OrderSnaphot::query()
                    ->where('id', $checkoutRequest['Siparis_ID'])
                    ->update([
                                 'totalPrice' => $totalPriceWithComission,
                                 'installment' => $checkoutRequest['Taksit']
                             ]);


        $client = $this->connect;

        $transactionsValueList = [
            "cardType" => $checkoutRequest['SanalPOS_ID'],
            "spid" => $checkoutRequest['SanalPOS_ID'],
            "guid" => env('GUID'),
            "cardHolderName" => $checkoutRequest['KK_Sahibi'],
            "cardNo" => $checkoutRequest['KK_No'],
            "monthOfExpireDate" => $checkoutRequest['KK_SK_Ay'],
            "yearOfExpireDate" => $checkoutRequest['KK_SK_Yil'],
            "creditCardCvc" => $checkoutRequest['KK_CVC'],
            "creditCardOwnerName" => "5372403939",
            "errorUrl" => env("API_URL") . "errorPayment",
            "succesUrl" => env("API_URL") . "successPayment",
            "orderID" => rand(0, 999999),
            "paymentUrl" => "http://localhost:3000/payment?order=" . $checkoutRequest['Siparis_ID'],
            "orderExplanation" => date("d-m-Y H:i:s") . " tarihindeki ödeme",
            "installment" => $checkoutRequest['Taksit'],
            "transactionPayment" => $totalPrice,
            "totalPayment" => $this->tlFormat($totalPriceWithComission),
            "transactionID" => $checkoutRequest['Siparis_ID'],
            "ipAdr" => "192.168.168.115"
        ];


        $data = new TotalPaymentTransaction(
            $transactionsValueList["cardType"],
            "",
            $transactionsValueList["guid"],
            $transactionsValueList["cardHolderName"],
            $transactionsValueList["cardNo"],
            $transactionsValueList["monthOfExpireDate"],
            $transactionsValueList["yearOfExpireDate"],
            $transactionsValueList["creditCardCvc"],
            $transactionsValueList["creditCardOwnerName"],
            $transactionsValueList["errorUrl"],
            $transactionsValueList["succesUrl"],
            $transactionsValueList["orderID"],
            $transactionsValueList["orderExplanation"],
            $transactionsValueList["installment"],
            $transactionsValueList["transactionPayment"],
            $transactionsValueList["totalPayment"],
            $transactionsValueList["transactionID"],
            $transactionsValueList["ipAdr"],
            $transactionsValueList["paymentUrl"]
        );

        $authObject = new Auth($transactionSecurityStr = env('CLIENT_CODE') .
            $transactionsValueList["guid"] .
            $transactionsValueList["spid"] .
            $transactionsValueList["installment"] .
            $transactionsValueList["transactionPayment"] .
            $transactionsValueList["totalPayment"] .
            $transactionsValueList["orderID"] .
            $transactionsValueList["errorUrl"] .
            $transactionsValueList["succesUrl"]);

        $data->Islem_Hash = $client->SHA2B64($authObject)->SHA2B64Result;
        $response = $client->TP_Islem_Odeme($data);

        if ($response->TP_Islem_OdemeResult->Sonuc == "1") {
            return response()->json(['url' => $response->TP_Islem_OdemeResult->UCD_URL]);
        } else {
            return response()->json(['err' => $response], 500);
        }


    }

    protected function tlFormat($money)
    {
        setlocale(LC_MONETARY, 'tr_TR');
        return str_replace('.', ',', $money);

    }

    public function successPayment($request)
    {
        if (isset($request->TURKPOS_RETVAL_Islem_ID)) {
            $order = OrderSnaphot::query()
                                 ->where('id', $request->TURKPOS_RETVAL_Islem_ID)
                                 ->firstOrFail();

            $orderSnapForm = json_decode($order->order);

            $storeCheckoutRequest = $this->buildRequestClassForStoreOrder($order, $orderSnapForm);

            $this->checkoutService->store($storeCheckoutRequest, $order->user_id);


        }
    }

    protected function buildRequestClassForStoreOrder($order, $orderSnapForm): StoreCheckoutRequest
    {
        $checkoutRequest = [
            'customer_email' => $orderSnapForm->customer_email,
            'customer_phone' => $orderSnapForm->customer_phone,
            'customer_first_name' => $orderSnapForm->customer_first_name,
            'customer_last_name' => $orderSnapForm->customer_last_name,
            'address' => $orderSnapForm->address,
            'coupon_id' => $orderSnapForm->coupon_id,
            'free_shipping' => $orderSnapForm->free_shipping,
            'payment_method' => $orderSnapForm->payment_method,
            'shipping_method' => $orderSnapForm->shipping_method,
            'coupon' => $orderSnapForm->coupon,
            'totalWithCommission' => $order->totalPrice,
            'installment' => $order->installment,
        ];

        $validator = Validator::make($checkoutRequest, [
            'customer_email' => [
                'required',
                'email'
            ],
            'customer_phone' => ['required'],
            'customer_first_name' => ['required'],
            'customer_last_name' => ['required'],
            'address' => [
                'required',
                'exists:addresses,id'
            ],
            'coupon_id' => [
                'nullable',
                'exists:coupons,id'
            ],
            'free_shipping' => [
                'required',
                'boolean'
            ],
            'payment_method' => [
                'required',
                Rule::in([
                             'bank_transfer',
                             'credit_cart'
                         ])
            ],
            'shipping_method' => ['required_if:free_shipping,1'],
            'coupon' => ['nullable']
        ]);


        $storeCheckoutRequest = new StoreCheckoutRequest($checkoutRequest);

        $storeCheckoutRequest->setValidator($validator);

        return $storeCheckoutRequest;
    }


}
