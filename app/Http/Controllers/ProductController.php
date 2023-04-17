<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\CategoryProductListRequest;
use FleetCart\Http\Resources\CategoryResource;
use FleetCart\Http\Resources\HomePageProductsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Events\ProductViewed;
use Modules\Product\Events\ShowingProductList;
use Modules\Product\Http\Controllers\ProductSearch;
use Modules\Product\Http\Middleware\SetProductSortOption;
use Modules\Review\Entities\Review;
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

        if (!$products) {
            $products = Product::query()
                               ->where('is_popular', 1)
                               ->inRandomOrder()
                               ->limit(20)->get();
            Redis::set('products', serialize($products));
        }

        if (!$sliders) {
            $sliders = Slider::query()
                             ->limit(3)
                             ->get();
            Redis::set('sliders', serialize($sliders));
        }

        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => HomePageProductsResource::collection($products),
                                    'sliders' => $sliders,
                                ]);
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
//        $relatedProducts = $product->relatedProducts()
//                                   ->forCard()
//                                   ->get();
//        $upSellProducts = $product->upSellProducts()
//                                  ->forCard()
//                                  ->get();
//        $review = $this->getReviewData($product);

        event(new ProductViewed($product));

        $data = [
            'product' => $product,
//            'relatedProducts' => $relatedProducts,
//            'upSellProducts' => $upSellProducts,
            //'review' => $review
        ];

        return response()->json($data);


    }

    private function getReviewData(Product $product)
    {
        if (!setting('reviews_enabled')) {
            return;
        }

        return Review::countAndAvgRating($product);
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

    public function getProductsByCategorySlug($slug, CategoryProductListRequest $request)
    {
        $products = Product::query()
                           ->whereHas('categories', function ($query) use ($slug) {
                               $query->where('slug', $slug);
                           });

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

        if ($request->has('brands') && $request->validated()['brands'] != null) {
            $array = explode(',', $request->validated()['brands']);
            $products->whereHas('brand', function ($q) use ($array) {
                $q->whereIn('id', $array);
            });
        }

        $category = Category::query()
                            ->where('slug', $slug)
                            ->firstOrFail();

        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => $products->paginate(12),
                                    'categories' => $this->getChildCategories($category),
                                    'brands' => $this->getBrands(),
                                ]);

    }

    public function getChildCategories(Category $category)
    {
        $categories = Category::query()
                              ->where('parent_id', $category->id)
                              ->get();

        if ($categories->count() < 1) {
            return Category::query()
                           ->where('id', $category->parent_id)
                           ->get();
        }
        return $categories;
    }

    public function getBrands()
    {
        return Brand::query()
                    ->where('is_active', 1)
                    ->get();
    }

    public function getProductsByBrandSlug($slug, CategoryProductListRequest $request)
    {
        $products = Product::query()
                           ->whereHas('brand', function ($query) use ($slug) {
                               $query->where('slug', $slug);
                           });

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

        if ($request->has('brands') && $request->validated()['brands'] != null) {
            $array = explode(',', $request->validated()['brands']);
            $products->whereHas('brand', function ($q) use ($array) {
                $q->whereIn('id', $array);
            });
        }

        $category = Category::query()
                            ->where('slug', $slug)
                            ->firstOrFail();

        event(new ShowingProductList($products));

        return response()->json([
                                    'products' => $products->paginate(12),
                                    'categories' => $this->getChildCategories($category),
                                    'brands' => $this->getBrands(),
                                ]);

    }

    public function suggestions(Request $request)
    {

    }


}
