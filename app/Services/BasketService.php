<?php

namespace FleetCart\Services;

use FleetCart\Http\Requests\StoreBasketRequets;
use FleetCart\Http\Requests\UpdateBasketRequests;
use Illuminate\Http\Request;


interface BasketService
{

    public function getBasketForCreditCard(int $id) :  \Illuminate\Http\JsonResponse;

    public function index(): \Illuminate\Http\JsonResponse;

    public function store(StoreBasketRequets $request): \Illuminate\Http\JsonResponse;

    public function delete(int $basket);

    public function storeAll(Request $request): bool;

    public function updateBasketQuantity(int $basketId, UpdateBasketRequests $request): \Illuminate\Http\JsonResponse;



}