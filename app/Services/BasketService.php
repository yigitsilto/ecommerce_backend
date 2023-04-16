<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\StoreBasketRequets;
use FleetCart\Http\Requests\UpdateBasketRequests;
use Illuminate\Http\Request;


interface BasketService
{

    public function getBasketForCreditCard($id);

    public function index();

    public function store(StoreBasketRequets $request);

    public function delete($basket);

    public function storeAll(Request $request);

    public function updateBasketQuantity($basketId, UpdateBasketRequests $request);

}