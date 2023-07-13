<?php

namespace FleetCart\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Order\Entities\Order;
use SoapClient;

class Cargo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cargo';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatik kargo servisi';
    private $url = 'http://webservices.yurticikargo.com:8080/KOPSWebServices/ShippingOrderDispatcherServices?wsdl';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function XMLPOST($PostAddress, $xmlData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PostAddress);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $result = curl_exec($ch);
        return $result;
    }

    public function delete()
    {

        $directory = 'public/pdf/panel';
        Storage::deleteDirectory($directory);
        Log::useDailyFiles(storage_path() . '/logs/kargo.log');
        Log::info('Klasor silindi');
    }

    public function handle()
    {
        $this->queryShipment();

    }

    public function queryShipment()
    {

        $this->client = new SoapClient($this->url);
        //Log::useDailyFiles(storage_path() . '/logs/kargo.log');
        $shop = Order::whereIn('cargo_status', [
            'NOP',
            'IND'
        ])
                     ->orderby('id', 'asc')
                     ->get();
        $cc = Order::whereIn('cargo_status', [
            'NOP',
            'IND'
        ])
                   ->count();
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


                    Log::info('Kargo İşlem Görmemiş: ' . $item->order_number . 'Kargo no : ' . $item->cargo_no);
                } else {
                    if ($status == 'IND') {
                        $message = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['operationMessage'];
                        $link = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['shippingDeliveryItemDetailVO']['trackingUrl'];
//                        $url = UrlShortener::driver('bitly')->shorten($link);

                        $number = preg_replace('/\D/', '', $link);

                        Order::where('id', $item->id)
                             ->update([
                                          'cargo_status' => 'IND',
                                          'cargo_url' => $link,
                                      ]);

                        // $this->sms($item->gsm, $number, $link, $item->order_number);


                        Log::info('Kargo Gitti: ' . $item->id . 'Kargo no : ' . $item->cargo_no . 'Link : ' . $link . 'Status ' . $message);
                    } else {
                        if ($status == 'DLV') {
                            $message = $array['ShippingDeliveryVO']['shippingDeliveryDetailVO']['operationMessage'];
                            Order::where('id', $item->id)
                                 ->update([
                                              'status' => 'DLV',
                                          ]);
                            Log::info('Kargo Teslim edildi: ' . $item->id . 'Kargo no : ' . $item->cargo_no . 'Status ' . $message);

                        }
                    }
                }
            }


        }

//        $this->delete();

    }

    public function sms($gsm, $cargo, $url, $order)
    {

        $user = Shopping_Cart::where('order_number', $order)
                             ->first();

        $text = 'Sayın ' . $user->name . ' siparişiniz kargoya verildi. Yeni siparişlerinizde görüşmek üzere. Kargo adı : YURTİCİ KARGO Takip No : ' . $cargo . ' Tek tıkla takip :' . $url . ' ';
        $username = '5326459793';
        $password = '123321';
        $orgin_name = 'FOTOGONDER';


        $xml = <<<EOS
   		 <request>
   			 <authentication>
   				 <username>{$username}</username>
   				 <password>{$password}</password>
   			 </authentication>

   			 <order>
   	    		 <sender>{$orgin_name}</sender>
   	    		 <sendDateTime>01/05/2013 18:00</sendDateTime>
   	    		 <message>
   	        		 <text>{$text}</text>
   	        		 <receipents>
   	            		 <number>{$gsm}</number>
   	        		 </receipents>
   	    		 </message>
   			 </order>
   		 </request>
EOS;


        $this->sendRequest('http://api.iletimerkezi.com/v1/send-sms', $xml, array('Content-Type: text/xml'));


        return $this;


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


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
}
