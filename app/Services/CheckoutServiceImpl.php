<?php

namespace FleetCart\Services;


use Exception;
use FleetCart\Basket;
use FleetCart\Http\Requests\StoreCheckoutRequest;
use FleetCart\OrderSnaphot;
use FleetCart\User;
use Illuminate\Support\Facades\DB;
use Modules\Address\Entities\Address;
use Modules\Checkout\Services\OrderService;
use Modules\Coupon\Entities\Coupon;
use Modules\User\Services\CustomerService;

class CheckoutServiceImpl implements CheckoutService
{

    private CustomerService $customerService;
    private OrderService $orderService;

    public function __construct(CustomerService $customerService, OrderService $orderService)
    {
        $this->customerService = $customerService;
        $this->orderService = $orderService;
    }


    public function store(StoreCheckoutRequest $request, $userId = null)
    {
        $user = User::query()
                    ->where('email', $request->validated()['customer_email'])
                    ->first();
        $user->email = $request->validated()['customer_email'];
        $user->last_name = $request->validated()['customer_first_name'];
        $user->first_name = $request->validated()['customer_last_name'];
        $user->phone = $request->validated()['customer_phone'];
        $user->save();


        $userId = is_null($userId) ? auth('api')
            ->user()
            ->getAuthIdentifier() : $userId;

        $array = $this->buildOrderValues($request, $userId);

        $request = $request->merge($array);

        try {
            DB::beginTransaction();
            $baskets = Basket::query()
                             ->whereHas('product', function ($q) {
                                 $q->where('is_active', 1);
                             })
                             ->with('product')
                             ->where('user_id', $userId)
                             ->get();
            if ($baskets->count() == 0) {
                return response()->json('Sepette ürün yok!', 500);
            }
            $order = $this->orderService->create($request, $baskets);
            Basket::query()
                  ->where('user_id', $userId)
                  ->delete();

            OrderSnaphot::query()
                        ->where('user_id', $userId)
                        ->delete();

            if (!is_null($request->coupon_id)) {
                $coupon = Coupon::query()
                                ->where('id', $request->coupon_id)
                                ->first();
                $coupon->used++;
                $coupon->save();
            }



            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            return null;
        }


    }

    /**
     * order için alınacak parametleri addresten çeker ve ekler
     * @param $request
     * @return array
     */

    private function buildOrderValues($request, $userId)
    {
        $addressValue = Address::query()
                               ->findOrFail($request->validated()['address']);

        $array = [
            'billing' => [
                'first_name' => $addressValue->first_name,
                'last_name' => $addressValue->last_name,
                'address_1' => $addressValue->address_1,
                'address_2' => $addressValue->address_2,
                'city' => $addressValue->city,
                'state' => $addressValue->state,
                'zip' => $addressValue->zip,
                'country' => $addressValue->country,
            ],
            'shipping' => [
                'first_name' => $addressValue->first_name,
                'last_name' => $addressValue->last_name,
                'address_1' => $addressValue->address_1,
                'address_2' => $addressValue->address_2,
                'city' => $addressValue->city,
                'state' => $addressValue->state,
                'zip' => $addressValue->zip,
                'country' => $addressValue->country,
            ],
        ];

        $array = array_merge($array, $this->getTotalValuesFromBasket($request, $userId));


        return $array;
    }

    public function getTotalValuesFromBasket($request, $userId = null)
    {

        $userId = is_null($userId) ? auth('api')
            ->user()
            ->getAuthIdentifier() : $userId;

        $prices = $this->calculateTotalPriceInBasket($userId);
        $totalPrice = $prices['totalPrice'];
        $subTotal = $prices['subTotal'];


        // kupon uygulandı
        $discount = 0;
        if (!is_null($request->coupon_id)) {
            $coupon = Coupon::query()
                            ->find($request->coupon_id);


            $amount = 0;
            if ($coupon->is_percent) {

                $discountPercent = floatval(str_replace(',', '.',
                                                        $coupon->value)) / 100; // Virgülle ayrılmış ondalık kısmı noktaya çeviriyoruz
                $amount = $totalPrice * $discountPercent;

            } else {
                $amount = $coupon->value->amount;
            }


            $discount = $amount;
            $totalPrice -= $discount;
        }

        $shippingMethod = null;
        $shippingCost = 0;
        if ($request->shipping_method instanceof \stdClass) {
            if (isset($request->shipping_method->price)) {
                $totalPrice += $request->shipping_method->price->amount;
                $shippingMethod = $request->shipping_method->id;
                $shippingCost = $request->shipping_method->price->amount;
            }
        } else {
            if (isset($request->shipping_method['price'])) {
                $totalPrice += $request->shipping_method['price']['amount'];
                $shippingMethod = $request->shipping_method['id'];
                $shippingCost = $request->shipping_method['price']['amount'];
            }
        }

        return [
            'sub_total' => $subTotal,
            'coupon_id' => $request->coupon_id,
            'discount' => $discount,
            'total' => $totalPrice,
            'shipping_method' => $shippingMethod,
            'shipping_cost' => $shippingCost,
            'payment_method' => $request->payment_method,
        ];

    }

    public function calculateTotalPriceInBasket($userId = null)
    {

        $userId = is_null($userId) ? auth('api')
            ->user()
            ->getAuthIdentifier() : $userId;

        $basket = Basket::query()
                        ->with('product')
                        ->where('user_id', $userId)
                        ->get();

        $totalPrice = 0;
        $subTotal = 0;

        foreach ($basket as $item) {
            $subTotal += $item->totalPriceNotFormatted;
            $totalPrice += $item->totalPriceNotFormatted;

        }

        return [
            'subTotal' => $subTotal,
            'totalPrice' => $totalPrice
        ];
    }

    public function createOrderSnapshot(StoreCheckoutRequest $request)
    {
        $orderSnapShot = OrderSnaphot::query()
                                     ->create([
                                                  'order' => json_encode($request->validated()),
                                                  'user_id' => auth('api')->user()->id
                                              ]);
        return $orderSnapShot->id;
    }


}
