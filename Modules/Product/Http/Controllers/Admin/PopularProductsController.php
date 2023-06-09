<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\Entities\PopularProduct;
use Modules\Product\Entities\Product;

class PopularProductsController
{

    public function index()
    {
        $popularProducts = PopularProduct::query()
                                         ->with('product')
                                         ->get()->pluck('product.id', 'id');

        $products = Product::query()
                           ->where('is_active', 1)
                           ->get()
                           ->pluck('name', 'id');

        return view('product::admin.products.popularProducts.index')->with(compact('products', 'popularProducts'));
    }

    public function store(Request $request)
    {

        $productIds = $request->get('id');

        // save the products array

        foreach ($productIds as $productId) {
            if (Product::query()
                       ->where('id', $productId)
                       ->doesntExist()) {
                continue;
            }

            PopularProduct::query()
                          ->firstOrCreate(
                              [
                                  'product_id' => $productId,
                              ],

                              [
                                  'product_id' => $productId,
                              ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Popüler ürünler başarıyla eklendi.');

    }

}
