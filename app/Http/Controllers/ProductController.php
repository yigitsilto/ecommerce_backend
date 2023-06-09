<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Blog;
use FleetCart\Http\Requests\BrandProductListRequest;
use FleetCart\Http\Requests\CategoryProductListRequest;
use FleetCart\Http\Resources\CategoryResource;
use FleetCart\Http\Resources\HomePageProductsResource;
use FleetCart\Http\Resources\ProductsByCategoryCollection;
use FleetCart\Http\Resources\RelatedProductResourceCollection;
use FleetCart\RelatedProduct;
use Illuminate\Support\Facades\Redis;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\PopularProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Events\ProductViewed;
use Modules\Product\Events\ShowingProductList;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\Product\Http\Middleware\SetProductSortOption;
use Modules\Slider\Entities\Slider;

class ProductController extends Controller
{
    use ProductSearch;

    public function __construct()
    {
        $this->middleware(SetProductSortOption::class)
             ->only('index');
    }

    /**
     * Display a listing of the resource.
     *

     */
    public function index()
    {

        $products = unserialize(Redis::get('products'));
        $sliders = unserialize(Redis::get('sliders'));
        $blogs = unserialize(Redis::get('blogs'));
//        $popularCategories = unserialize(Redis::get('popularCategories'));


//        if (!$popularCategories) {
//            $popularCategories = Category::inRandomOrder()
//                                         ->whereHas('products')
//                                         ->with('files')
//                                         ->where('is_active', true)
//                                         ->where('is_popular', true)
//                                         ->limit(6)
//                                         ->get();
//            Redis::set('popularCategories', serialize($popularCategories));
//        }

        if (!$products) {
            $products = PopularProduct::query()
                                      ->with([
                                                 'product' => function ($query) {
                                                     $query->where('is_active', true);
                                                 },
                                                 'product.brand'
                                             ])
                                      ->whereHas('product', function ($query) {
                                          $query->where('is_active', true);
                                      })
                                      ->inRandomOrder()
                                      ->limit(16)
                                      ->get();


            Redis::set('products', serialize($products));

        }


        if (!$sliders) {
            $sliders = Slider::query()
                             ->limit(3)
                             ->get();
            Redis::set('sliders', serialize($sliders));
        }

        if (!$blogs) {
            $blogs = Blog::query()
                         ->select([
                                      'id',
                                      'title',
                                      'slug',
                                      'short_description',
                                      'cover_image',
                                      'created_at'
                                  ])
                         ->limit(2)
                         ->get();
            Redis::set('blogs', serialize($blogs));
        }


        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => HomePageProductsResource::collection($products),
                                    'sliders' => $sliders,
                                    'blogs' => $blogs,
                                    //                                    'categories' => $popularCategories
                                ]);
    }

    public function relatedProducts($slug)
    {
        $product = Product::findBySlug($slug);


        $relatedProducts = RelatedProduct::query()
                                         ->with([
                                                    'product',
                                                    'product.brand'
                                                ])
                                         ->where('product_id', $product->id)
                                         ->paginate(12);


        if ($relatedProducts->count() == 0) {
            $relatedProducts = RelatedProduct::query()
                                             ->distinct('product_id')
                                             ->inRandomOrder()
                                             ->with([
                                                        'product',
                                                        'product.brand'
                                                    ])
                                             ->paginate(12);
        }


        return response()->json(new RelatedProductResourceCollection($relatedProducts));

    }

    /**
     * Show the specified resource.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {

        $product = Product::findBySlug($slug);

        event(new ProductViewed($product));

        $product->load('meta');

        $data = [
            'product' => $product,
            //            'upSellProducts' => $upSellProducts,
            //'review' => $review
        ];


        return response()->json($data);


    }

    public function categoriesForProduct()
    {

        $query = unserialize(Redis::get('categoryWithProducts'));

        if (!$query) {
            $query = Category::inRandomOrder()
                             ->whereHas('products')
                             ->with([
                                        'products' => function ($query) {
                                            $query->where('is_active', 1)
                                                  ->where('is_popular', 1)
                                                  ->inRandomOrder()
                                                  ->limit(20);
                                        },
                                    ])
                             ->where('is_active', true)
                             ->where('is_popular', true)
                             ->limit(6)
                             ->get();
            Redis::set('categoryWithProducts', serialize($query));
        }


        return CategoryResource::collection($query);
    }

    public function getProductsByFilter($category, CategoryProductListRequest $request)
    {

        $products = Product::query()
                           ->whereHas('categories', function ($query) use ($category) {
                               $query->where('slug', $category);
                           })
                           ->where('is_active', 1)
                           ->with([
                                      'brand',
                                      'categories' => function ($query) use ($category) {
                                          $query->where('slug', $category);
                                      },
                                      'filterValues'
                                  ]);


        if ($request->has('order')) {
            if (($request->validated()['order']) == 'orderByPrice') {
                $products = $products->orderBy('price', 'asc');
            } elseif (($request->validated()['order']) == 'orderByPriceAsc') {
                $products = $products->orderBy('price', 'desc');
            } elseif (($request->validated()['order']) == 'orderByName') {
                $products = $products->orderBy('slug', 'desc');
            } elseif (($request->validated()['order']) == 'orderByNameAsc') {
                $products = $products->orderBy('slug', 'asc');
            }
        }

        if ($request->has('filter') && !is_null($request->validated()['filter'])) {
            $filters = explode(',', $request->validated()['filter']);
            $products = $products->whereHas('filterValues.filterValue', function ($query) use ($filters) {
                $query->whereIn('slug', $filters);
            });

        }


        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => new ProductsByCategoryCollection($products->paginate(12)),
                                ]);

    }

    public function getProductsByBrandSlug($brand, BrandProductListRequest $request)
    {

        $products = Product::query()
                           ->whereHas('brand', function ($query) use ($brand) {
                               $query->where('slug', $brand);
                           })
                           ->where('is_active', 1)
                           ->with([
                                      'brand' => function ($query) use ($brand) {
                                          $query->where('slug', $brand);
                                      },
                                      'categories',
                                      'filterValues'
                                  ]);

        if ($request->has('order')) {
            if (($request->validated()['order']) == 'orderByPrice') {
                $products = $products->orderBy('price', 'asc');
            } elseif (($request->validated()['order']) == 'orderByPriceAsc') {
                $products = $products->orderBy('price', 'desc');
            } elseif (($request->validated()['order']) == 'orderByName') {
                $products = $products->orderBy('slug', 'desc');
            } elseif (($request->validated()['order']) == 'orderByNameAsc') {
                $products = $products->orderBy('slug', 'asc');
            }
        }

        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => new ProductsByCategoryCollection($products->paginate(12)),
                                ]);


    }

}
