<?php

namespace FleetCart\Services;

use FleetCart\Http\Controllers\Controller;
use FleetCart\Http\Controllers\Shopping_Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Address\Entities\Address;
use Modules\Order\Entities\Order;
use Ramsey\Uuid\Uuid;
use SoapClient;
use Stancl\Tenancy\UUIDGenerator;


class KargoService extends Controller
{


    private $url = 'http://webservices.yurticikargo.com:8080/KOPSWebServices/ShippingOrderDispatcherServices?wsdl';
//    private $url = 'http://testwebservices.yurticikargo.com:9090/KOPSWebServices/ShippingOrderDispatcherServices?wsdl';


    public function CreateShipment($order)
    {

        $this->client = new SoapClient($this->url);


        $id = $order['id'];

        $params =
            [


                "cargoKey" => $order['id'] . Uuid::uuid4()->toString(),
                "invoiceKey" => $order['id'] . Uuid::uuid4()->toString(),
                "receiverCustName" => $order['shipping_first_name'] . ' ' . $order['shipping_last_name'],
                "receiverAddress" => $order['shipping_address_1'],
                "receiverPhone1" => $order['customer_phone'],
                "receiverPhone2" => "",
                "receiverPhone3" => "",
                "cityName" => $order['shipping_city'],
                "townName" => $order['shipping_state'],


                "custProdId" => '1',
                "desi" => "3,5",
                "kg" => '7,6',
                "cargoCount" => "1",
                "waybillNo" => '',
                "specialField1" => "",
                "specialField2" => "",
                "specialField3" => "",
                "ttCollectionType" => "",
                "ttInvoiceAmount" => '',
                "ttDocumentId" => "",
                "ttDocumentSaveType" => '',
                "orgReceiverCustId" => "",
                "description" => '',
                "taxNumber" => "",
                "taxOfficeId" => "",
                "taxOfficeName     " => "",
                "orgGeoCode" => "",
                "privilegeOrder" => "",
                "dcSelectedCredit" => "",
                "dcCreditRule" => "",
                "emailAddress " => "",

            ];


        $data = array_merge(
            [
                "wsUserName" => 'MASERAGO',
                "wsPassword" => 'A23368E29D151A',
                "wsLanguage" => 'TR',
                "userLanguage" => "TR",

            ],
            ["ShippingOrderVO" => $params]
        );

        $CreateShipmentData = $this->client->createShipment($data);


        $array = json_decode(json_encode($CreateShipmentData), true);

        if ($array['ShippingOrderResultVO']['outFlag'] == 0) {
            $data = [

                'cargo_no' => $array['ShippingOrderResultVO']['shippingOrderDetailVO']['cargoKey'],
                'cargo_key' => $array['ShippingOrderResultVO']['shippingOrderDetailVO']['cargoKey'],


            ];
            Order::query()
                 ->where('id', $id)
                 ->update([
                              'cargo_no' => $data['cargo_no'],
                              'cargo_key' => $data['cargo_key'],
                              'cargo_status' => 'NOP'
                          ]);


            return true;

        } else {

            throw new \Exception($array['ShippingOrderResultVO']['shippingOrderDetailVO']['errMessage']);

        }


    }

    function sendRequest($site_name, $send_xml, $header_type)
    {

        //die('SITENAME:'.$site_name.'SEND XML:'.$send_xml.'HEADER TYPE '.var_export($header_type,true));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $site_name);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $send_xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_type);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);

        return $result;
    }


    public function sms($gsm, $cargo, $url, $order)
    {

//        $user = Shopping_Cart::where('order_number', $order)->first();
//
//        $text = 'Sayın ' . $user->name . ' siparişiniz kargoya verildi. Yeni siparişlerinizde görüşmek üzere. Kargo adı : YURTİCİ KARGO Takip No : ' . $cargo . ' Tek tıkla takip :' . $url . ' ';
//        $username = '5326459793';
//        $password = '2017Usebox!';
//        $orgin_name = 'FOTOGONDER';
//
//
//        $xml = <<<EOS
//   		 <request>
//   			 <authentication>
//   				 <username>{$username}</username>
//   				 <password>{$password}</password>
//   			 </authentication>
//
//   			 <order>
//   	    		 <sender>{$orgin_name}</sender>
//   	    		 <sendDateTime>01/05/2013 18:00</sendDateTime>
//   	    		 <message>
//   	        		 <text>{$text}</text>
//   	        		 <receipents>
//   	            		 <number>{$gsm}</number>
//   	        		 </receipents>
//   	    		 </message>
//   			 </order>
//   		 </request>
//EOS;
//
//
//        $this->sendRequest('http://api.iletimerkezi.com/v1/send-sms', $xml, array('Content-Type: text/xml'));
//
//
//        return $this;


    }


    function XMLPOST($PostAddress, $xmlData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PostAddress);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $result = curl_exec($ch);
        return $result;
    }

   /* public function sms($gsm, $cargo, $url, $order)
    {
        $user = Shopping_Cart::where('order_number', $order)->first();

        $text = 'Sayın ' . $user->name . ' siparişiniz kargoya verildi. Yeni siparişlerinizde görüşmek üzere. Kargo adı : YURTİCİ KARGO Takip No : ' . $cargo . ' Tek tıkla takip :' . $url . ' ';
        //sms Gönder
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<mainbody>
	<header>
		<company>MASERA</company>
        <usercode>5326459793</usercode>
        <password>123masera</password>
		<startdate></startdate>
		<stopdate></stopdate>
	    <type>1:n</type>
        <msgheader>MASERA</msgheader>
        </header>
		<body>
		<msg><![CDATA['.$text.' Operatör kodu : ]]></msg>
		<no>' . $gsm . '</no>
		</body>
</mainbody>';

        $this->XMLPOST('http://api.netgsm.com.tr/xmlbulkhttppost.asp', $xml);

        return;

    }
*/

    public function queryShipment()
    {

        $this->client = new SoapClient($this->url);
        Log::useDailyFiles(storage_path() . '/logs/kargo.log');
        $shop = Shopping_Cart::whereIn('status', [2, 3])->where('sms', 0)->orderby('id','asc')->get();
        $cc = Shopping_Cart::whereIn('status', [2, 3])->where('sms', 0)->count();
//        $count = 0;

        foreach ($shop as $item) {
//            $count++;


            echo $item->cargo_no . '<br>';
            sleep(2);
//            if ($count != count($shop) && ($count % 5) == 0) {
                $data = array_merge(
                    [
                        "wsUserName" => 'MASERAGO',
                        "wsPassword" => 'A23368E29D151A',
                        "wsLanguage" => "TR",
                        "userLanguage" => "TR",
                        "keys" => $item->cargo_no,
                        "keyType" => "0",
                        "addHistoricalData" => 'true',
                        "onlyTracking" => 'false',

                    ]

                );

                $CreateShipmentData = $this->client->queryShipment($data);

                $array = json_decode(json_encode($CreateShipmentData), true);


                $err = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['jobId'];

                if ($err != 0) {


                    $status = @$array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['operationStatus'];

                    if ($status == 'NOP') {

                        Log::info('Kargo İşlem Görmemş: ' . $item->order_number . 'Kargo no : ' . $item->cargo_no );
                    }



                   else if ($status == 'IND') {
                       $message = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['operationMessage'];
                       $link = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['shippingDeliveryItemDetailVO']['trackingUrl'];
//                        $url = UrlShortener::driver('bitly')->shorten($link);

                        $number = preg_replace('/\D/', '', $link);

                        Shopping_Cart::where('order_number', $item->order_number)->update(['sms' => 1, 'status' => '3', 'status_text' => 'Kargoya Verildi', 'cargo_url' => $link]);

                        $this->sms($item->gsm, $number, $link, $item->order_number);


                        Log::info('Kargo Gitti: ' . $item->id . 'Kargo no : ' . $item->cargo_no . 'Link : ' . $link. 'Status '. $message );
                    }
                    else if ($status == 'DLV') {
                        Shopping_Cart::where('order_number', $item->order_number)->update(['status' => '4', 'status_text' => 'Teslim Edildi']);
                        Log::info('Kargo Gitmedi: ' . $item->id . 'Kargo no : ' . $item->cargo_no . 'Status '. $message);

                    }
                }




            }

        }



    public function status($id)
    {

        $this->client = new SoapClient($this->url);


        $data = array_merge(
            [
                "wsUserName" => 'MASERAGO',
                "wsPassword" => 'A23368E29D151A',
                "wsLanguage" => "TR",
                "userLanguage" => "TR",
                "keys" => $id,
                "keyType" => "0",
                "addHistoricalData" => 'true',
                "onlyTracking" => 'false',

            ]

        );

        $CreateShipmentData = $this->client->queryShipment($data);


        $array = json_decode(json_encode($CreateShipmentData), true);
//        $err = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['errCode'];


        dd($array);


////        $status = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['operationStatus'];
//        $link = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['shippingDeliveryItemDetailVO']['trackingUrl'];
////        $url = UrlShortener::driver('bitly')->shorten($link);
//
//        $number = preg_replace('/\D/', '', $link);
//
//        return $number;







    }

    public function cancelShipment(Request $request)
    {

        $this->client = new SoapClient($this->url);

        $ekle = $request->except('_token');

        $data =
            [
                "wsUserName" => 'MASERAGO',
                "wsPassword" => 'A23368E29D151A',
                "wsLanguage" => "TR",
                "userLanguage" => "TR",
                "cargoKeys" => [$request->cargo_no]
            ];


        $cancelShipment = $this->client->cancelShipment($data);

        $array = json_decode(json_encode($cancelShipment), true);


        if ($array['ShippingOrderResultVO']['outFlag'] == 0) {
            $data = [

                'cargo_no' => '',
                'cargo_key' => '',

            ];

            Shopping_Cart::where('order_number', $request->key)->update($data);

            return redirect()->back()->with(['notify' => 'true', 'text' => 'Kargo başarı ile iptal edildi.']);

        } else {

            return redirect()->back()->with(['notifyerror' => 'true', 'text' => $array['ShippingOrderResultVO']['shippingCancelDetailVO']['errMessage']]);

        }


    }


    public function barcode($id)
    {

        $shopping = Shopping_Cart::where('id', $id)->first();


        return view('backend.cargos.barkod', compact('shopping'));


//


//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C39");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C39+");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C39E");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C39E+");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C93");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "S25");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "S25+");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "I25");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "I25+");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C128");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C128A");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C128B");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "C128C");
//        echo DNS1DFacade::getBarcodeHTML("44455656", "EAN2");
//        echo DNS1DFacade::getBarcodeHTML("4445656", "EAN5");
//        echo DNS1DFacade::getBarcodeHTML("4445", "EAN8");
//        echo DNS1DFacade::getBarcodeHTML("4445", "EAN13");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "UPCA");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "UPCE");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "MSI");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "MSI+");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "POSTNET");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "PLANET");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "RMS4CC");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "KIX");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "IMB");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "CODABAR");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "CODE11");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "PHARMA");
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "PHARMA2T");

//        echo DNS1DFacade::getBarcodeSVG("4445645656", "PHARMA2T",3,33,"green", true);
//        echo DNS1DFacade::getBarcodeHTML("4445645656", "PHARMA2T");
//        echo '<img src="data:image/png,' . DNS1DFacade::getBarcodePNG("4", "C39+") . '" alt="barcode"   />';
//        echo DNS1DFacade::getBarcodePNGPath("4445645656", "PHARMA2T");
//        echo '<img src="data:image/png;base64,' . DNS1DFacade::getBarcodePNG("4", "C39+") . '" alt="barcode"   />';

    }

}