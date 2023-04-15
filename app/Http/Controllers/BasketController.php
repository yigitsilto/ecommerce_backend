<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Basket;
use FleetCart\Exceptions\BaseException;
use FleetCart\Http\Requests\StoreBasketRequets;
use FleetCart\Http\Requests\UpdateBasketRequests;
use FleetCart\OrderSnaphot;
use Illuminate\Http\Request;
use Modules\Option\Entities\OptionValue;
use Modules\Product\Entities\Product;
use Modules\Support\Money;

class BasketController extends Controller
{

    public function getBasketForCreditCard($id)
    {
        $order = OrderSnaphot::query()
                             ->where('id', $id)
                             ->first();
        if (!$order) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($order->user_id != auth('api')
                ->user()
                ->getAuthIdentifier()) {
            return response()->json(['error' => 'Not authorized'], 405);
        }
        return response()->json(['data' => json_decode($order->order)]);
    }

    public function index()
    {


        $basket = Basket::query()
                        ->whereHas('product')
                        ->with('product')
                        ->where('user_id', auth('api')->id())
                        ->get();

        $itemPrice = 0;
        foreach ($basket as $item) {

            // qty is not enough delete item
                if ($item->product->qty < $item->quantity) {
                    $item->delete();
                    continue;
                }

            $normalPrice = $item->product->normalPrice ?? 0;
            if (isset($item->product->special_price)) {
                $normalPrice = $item->product->special_price->amount;
            }


            $options = json_decode($item->options);

            if (count($options) > 0) {
                foreach ($options as $option) {
                    $optionValue = OptionValue::query()
                                              ->where('option_id', $option->optionId)
                                              ->where('id', $option->valueId)
                                              ->first();

                    $normalPrice += isset($optionValue->price) ? $optionValue->price->amount : 0;
                }
            }


            $item->price = Money::inDefaultCurrency($normalPrice * $item->quantity);

        }


        $decimalNumber = null;
        if (setting('free_shipping_enabled')) {
            $decimalNumber = number_format(setting('free_shipping_min_amount'), 4, '.',
                                           ''); // converts 1000 to 10,000.0000
        }


        return response()->json([
                                    'basket' => $basket,
                                    'free_shipping_amount' => $decimalNumber
                                ]);
    }

    public function storeAll(Request $requets)
    {

        foreach ($requets->all() as $item) {
            $product = Product::with('options')
                              ->select('id', 'manage_stock', 'qty')
                              ->findOrFail($item['product_id']);

            $qty = 10;
            if ($product->manage_stock) {
                if ($product->qty < 10) {
                    $qty = $product->qty;
                }
            }

            if ($qty < $item["quantity"]) {
                return BaseException::responseServerError('Stok yeterli değil');
            }

            $basket = Basket::query()
                            ->with('product')
                            ->where('user_id', auth('api')->id())
                            ->where('product_id', $item['product_id'])
                            ->first();

            if ($basket) {

                $basket->quantity++;
                $basket->save();
            } else {
                Basket::create([
                                   'user_id' => auth('api')->id(),
                                   'product_id' => $item['product_id'],
                                   'quantity' => $item['quantity'],
                                   'options' => json_encode($item['options'])
                               ]);
            }

        }

        return true;


    }

    public function store(StoreBasketRequets $request)
    {

        $basket = Basket::query()
                        ->with('product')
                        ->where('user_id', auth('api')->id())
                        ->where('product_id', $request->product_id)
                        ->first();


        if ($basket) {

            $basket->quantity++;
            $basket->save();
        } else {
            $basket = Basket::create([
                                         'user_id' => auth('api')->id(),
                                         'product_id' => $request->product_id,
                                         'quantity' => $request->quantity,
                                         'options' => json_encode($request->options)
                                     ]);
        }
        return response()->json($basket);
    }

    public function updateBasketQuantity($basketId, UpdateBasketRequests $request)
    {
        $basket = Basket::find($basketId);
        if ($basket->product->qty < $request->quantity) {
            return BaseException::responseServerError('Stok yeterli değil');
        }
        // is user have a product control
        if ($basket->user_id == auth('api')->id()) {

            $basket->update([
                                'quantity' => $request->quantity
                            ]);
        }

        return response()->json($basket);


    }

    public function delete($basket)
    {
        $basket = Basket::query()
                        ->find($basket);
        if (!$basket) {
            return BaseException::responseServerError('Ürün bulunamadı');
        }
        if ($basket->user_id == auth('api')->id()) {
            $basket->delete();
            return 1;
        } else {
            return response()->json(['error' => "unauthorized"], 401);
        }
    }
}
