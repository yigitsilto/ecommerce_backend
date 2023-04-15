<?php

namespace FleetCart\Repositories\Product;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository implements ProductRepositoryInterface
{
    public function index(){

       return QueryBuilder::for(Product::class)
           ->allowedIncludes(['brand'])
            ->allowedFilters('slug')
            ->paginate(12);
    }

    public function show(int $id):Model{
        return QueryBuilder::for(Product::class)
            ->where('id',$id)
            ->with('brand')
            ->first();
    }

}

