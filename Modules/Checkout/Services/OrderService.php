<?php

namespace Modules\Checkout\Services;

use FleetCart\Basket;
use Modules\Cart\CartTax;
use Modules\Cart\CartItem;
use Modules\Cart\Facades\Cart;
use Modules\Order\Entities\Order;
use Modules\Address\Entities\Address;
use Modules\FlashSale\Entities\FlashSale;
use Modules\Currency\Entities\CurrencyRate;
use Modules\Address\Entities\DefaultAddress;
use Modules\Product\Entities\Product;
use Modules\Shipping\Facades\ShippingMethod;

class OrderService
{
    public function create($request,$basket)
    {
       // $this->mergeShippingAddress($request);
       // $this->saveAddress($request);
        $this->addShippingMethodToCart($request);

        return tap($this->store($request), function ($order) use($basket) {
            $this->storeOrderProducts($order,$basket);
          //  $this->storeOrderDownloads($order,$basket); // TODO gerekirse eklenicek
           // $this->storeFlashSaleProductOrders($order,$basket);
           // $this->incrementCouponUsage($order);
           // $this->attachTaxes($order);
            $this->reduceStock($order);
        });
    }

    private function mergeShippingAddress($request)
    {
        $request->merge([
            'shipping' => $request->ship_to_a_different_address ? $request->shipping : $request->billing,
        ]);
    }

    private function saveAddress($request)
    {
        if (auth()->guest()) {
            return;
        }

        if ($request->newBillingAddress) {
            $address = auth()->user()->addresses()->create(
                $this->extractAddress($request->billing)
            );


            $this->makeDefaultAddress($address);
        }

        if ($request->ship_to_a_different_address && $request->newShippingAddress) {
            auth()->user()->addresses()->create(
                $this->extractAddress($request->shipping)
            );
        }
    }

    private function extractAddress($data)
    {
        return [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'country' => $data['country'],
        ];
    }

    private function makeDefaultAddress(Address $address)
    {
        if (auth()->user()->addresses()->count() > 1) {
            return;
        }

        DefaultAddress::create([
            'address_id' => $address->id,
            'customer_id' => auth()->id(),
        ]);
    }

    private function addShippingMethodToCart($request)
    {
        if (! Cart::allItemsAreVirtual() && ! Cart::hasShippingMethod()) {
            Cart::addShippingMethod(ShippingMethod::get($request->shipping_method));
        }
    }

    private function store($request)
    {
        return Order::create([
                                 'customer_id' => auth()->id(),
                                 'customer_email' => $request->customer_email,
                                 'customer_phone' => $request->customer_phone,
                                 'customer_first_name' => $request->billing['first_name'],
                                 'customer_last_name' => $request->billing['last_name'],
                                 'billing_first_name' => $request->billing['first_name'],
                                 'billing_last_name' => $request->billing['last_name'],
                                 'billing_address_1' => $request->billing['address_1'],
                                 'billing_address_2' => $request->billing['address_2'] ?? null,
                                 'billing_city' => $request->billing['city'],
                                 'billing_state' => $request->billing['state'],
                                 'billing_zip' => $request->billing['zip'],
                                 'billing_country' => $request->billing['country'],
                                 'shipping_first_name' => $request->shipping['first_name'],
                                 'shipping_last_name' => $request->shipping['last_name'],
                                 'shipping_address_1' => $request->shipping['address_1'],
                                 'shipping_address_2' => $request->shipping['address_2'] ?? null,
                                 'shipping_city' => $request->shipping['city'],
                                 'shipping_state' => $request->shipping['state'],
                                 'shipping_zip' => $request->shipping['zip'],
                                 'shipping_country' => $request->shipping['country'],
                                 'sub_total' => $request->sub_total,
                                 'shipping_method' => $request->shipping_method,
                                 'shipping_cost' => $request->shipping_cost,
                                 'coupon_id' => $request->coupon_id,
                                 'discount' => $request->discount,
                                 'total' => $request->total,
                                 'payment_method' => $request->payment_method,
                                 'currency' => currency(),
                                 'currency_rate' => CurrencyRate::for(currency()),
                                 'locale' => locale(),
                                 'status' => Order::PENDING_PAYMENT,
                                 'note' => $request->order_note,
                                 'totalWithCommission' => $request->totalWithCommission ?? null,
                                 'installment' => $request->installment ?? null,
        ]);
    }

    private function storeOrderProducts(Order $order,$basket)
    {
        foreach ($basket as $cartItem){
            $order->storeProductsForApi($cartItem);
        }
    }

    private function storeOrderDownloads(Order $order)
    {
        Cart::items()->each(function (CartItem $cartItem) use ($order) {
            $order->storeDownloads($cartItem);
        });
    }

    // TODO gerekirse yapılacak
    private function storeOrderDownloadsForApi(Order $order,$basket)
    {
        foreach ($basket as $cartItem){
            $order->storeDownloads($cartItem);
        }
    }

    private function storeFlashSaleProductOrders(Order $order,$basket)
    {
        foreach ($basket as $cartItem){


            FlashSale::pivot($cartItem->product)
                ->orders()
                ->attach([
                    $cartItem->product->id => [
                        'order_id' => $order->id,
                        'qty' => $cartItem->quantity,
                    ],
                ]);
        }
    }

    private function incrementCouponUsage()
    {
        Cart::coupon()->usedOnce();
    }

    private function attachTaxes(Order $order)
    {
        Cart::taxes()->each(function (CartTax $cartTax) use ($order) {
            $order->attachTax($cartTax);
        });
    }

    public function reduceStock($order)
    {
        foreach ($order->products as $product){
           if (!empty($product->product->qty)){
               Product::query()->where('id',$product->product->id)->decrement('qty',$product->qty);

           }
        }

    }

    public function delete(Order $order)
    {
        $order->delete();

        Cart::restoreStock();
    }
}
