<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\StoreBasketRequets;
use FleetCart\Http\Requests\UpdateBasketRequests;
use Illuminate\Http\Request;


interface BasketService
{

    public function getBasketForCreditCard(int $id);

    public function index();

    public function store(StoreBasketRequets $request);

    public function delete(int $basket);

    public function storeAll(Request $request): bool;

    public function updateBasketQuantity(int $basketId, UpdateBasketRequests $request): \Illuminate\Http\JsonResponse;



}