<?php

namespace FleetCart\Services;

use FleetCart\ParamConnectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Kredi kartı bin numarasına göre kart bilgilerini döner
 * Bin: kart ilk 6 numarası
 * Kart bilgileri: world,axess,visa vs.
 */
class CardTypeBinApiServiceImpl implements CardTypeBinApiService
{
    private $connect;
    private $globallyUniqueIdentifier;
    private $paramPosService;

    public function __construct(ParamConnectionHelper $paramConnectionHelper, ParamPosService $paramPosService)
    {
        $this->paramPosService = $paramPosService;
        $this->globallyUniqueIdentifier = env('GUID');
        if ($paramConnectionHelper->getConnection() == null) {
            $paramConnectionHelper->setConnection();
        }
        $this->connect = $paramConnectionHelper->getConnection();
    }

    /**
     * KArt bilgilerini döner taksit sayısı ve tipi vs
     * @param Request $request
     * @return array
     */
    public function getCardInformations(Request $request)
    {

        $cardNumber = str_replace(' ', '',$request->get('cardNumber'));
        $cardNumber = substr($cardNumber, 0, 6);

        $cardInformations = $this->getCardType($cardNumber);

        // taksit bilgilerini döner
        $insltamment = $this->paramPosService->getAllInstalments();
        $installmentsArr = null;

        // banka tipine göre taksit eşleştirir
        foreach ($insltamment->DT_Ozel_Oranlar_SK as $instData) {
            if (isset($instData->Kredi_Karti_Banka)) {
                if (Str::slug(strtolower($instData->Kredi_Karti_Banka->__toString())) == Str::slug(strtolower
                                                                                                   ($cardInformations['cardBrand']))) {
                    $installmentsArr = $instData;
                }
            }
        }

        return [
            'cardType' => strtolower($cardInformations['cardType']),
            'cardBrand' => $cardInformations['cardBrand'],
            'installment' => $installmentsArr
        ];


    }

    /**
     * Param apisinden bin numarasına göre kart tipini döner
     * @param string $cardNumber
     * @return array
     */
    protected function getCardType(string $cardNumber)
    {

        $installmentsObj = new \stdClass();
        $installmentsObj->G = new \stdClass();
        $installmentsObj->G->CLIENT_CODE = env('CLIENT_CODE');
        $installmentsObj->G->CLIENT_USERNAME = env('CLIENT_USERNAME');
        $installmentsObj->G->CLIENT_PASSWORD = env('CLIENT_PASSWORD');
        $installmentsObj->GUID = env('GUID');
        $installmentsObj->BIN = $cardNumber;


        $specialRatioConnection = $this->connect->BIN_SanalPos($installmentsObj); // soap dan gelen bir fonskiyon kredi kart taksit oranlarını listeler


        $resultFromConnection = $specialRatioConnection->BIN_SanalPosResult;


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


        return [
            'cardBrand' => $ratioListXml->Temp->Kart_Marka->__toString(),
            'cardType' => $ratioListXml->Temp->Kart_Org->__toString()
        ];


    }
}
