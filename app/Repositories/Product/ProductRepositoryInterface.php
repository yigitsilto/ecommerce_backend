<?php

namespace FleetCart\Repositories\Product;

use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{
    public function index();
    public function show(int $id):Model;

}

