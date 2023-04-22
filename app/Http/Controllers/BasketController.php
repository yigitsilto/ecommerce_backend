<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\StoreBasketRequets;
use FleetCart\Http\Requests\UpdateBasketRequests;
use FleetCart\Services\BasketService;
use Illuminate\Http\Request;

class BasketController extends Controller
{

    private BasketService $basketService;


    public function __construct(BasketService $basketService)
    {
        $this->basketService = $basketService;
    }

    public function getBasketForCreditCard(int $id)
    {
        return response()->json(['data' => $this->basketService->getBasketForCreditCard($id)]);
    }

    public function index()
    {
        return response()->json($this->basketService->index());
    }

    public function delete(int $basket)
    {
        return $this->basketService->delete($basket);
    }

    public function storeAll(Request $request)
    {
        return $this->basketService->storeAll($request);
    }

    public function store(StoreBasketRequets $request)
    {
        return $this->basketService->store($request);
    }

    public function updateBasketQuantity(int $basketId, UpdateBasketRequests $request)
    {
        return $this->basketService->updateBasketQuantity($basketId, $request);
    }
}
