<?php

use FleetCart\Http\Controllers\AddressController;
use FleetCart\Http\Controllers\AuthController;
use FleetCart\Http\Controllers\BasketController;
use FleetCart\Http\Controllers\BrandController;
use FleetCart\Http\Controllers\CategoryController;
use FleetCart\Http\Controllers\CheckoutController;
use FleetCart\Http\Controllers\ProductController;
use FleetCart\Http\Controllers\ReviewController;
use FleetCart\Http\Controllers\UserController;
use FleetCart\Http\Controllers\WishListController;
use FleetCart\Http\Middleware\JWTMiddleware;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Modules\Product\Entities\Product;
use Modules\Product\Events\ShowingProductList;
use Modules\Product\Http\Controllers\SuggestionController;
use Modules\Slider\Entities\Slider;


Route::get('kargo-test', [\FleetCart\Http\Controllers\KargoController::class, 'queryShipment']);

/** Product import */
Route::get('import-kore',[\FleetCart\Http\Controllers\SettingsController::class,'importKore']);
/** Product import end */

Route::post('forgot-password', [\Modules\User\Http\Controllers\AuthController::class, 'postReset'])->name("forgot");
Route::post('password/reset/{email}/{code}', [\Modules\User\Http\Controllers\AuthController::class, 'postResetComplete'])->name("forgot");



/** Product Controller */
Route::resource('filters', \FleetCart\Http\Controllers\FiltersController::class)->only('index');
Route::resource('brands', BrandController::class)->only('index');
Route::resource('homePage', ProductController::class)->only('index');
Route::get('products/{slug}', [ProductController::class, 'show']);
Route::get('relatedProducts/{slug}', [ProductController::class, 'relatedProducts']);
Route::get('categoriesForProducts', [ProductController::class, 'categoriesForProduct']); // TODO silinebilir temizlik
Route::get('products/category/{category}', [ProductController::class, 'getProductsByFilter']);
Route::get('productsByBrand/{brand}', [ProductController::class, 'getProductsByBrandSlug']);
Route::get('suggestions', [SuggestionController::class, 'index']);
Route::get('search', [SuggestionController::class, 'searchProducts']);
Route::get('settings',[\FleetCart\Http\Controllers\SettingsController::class,'index']);
Route::get('page/{slug}',[\FleetCart\Http\Controllers\SettingsController::class,'getPageById']);
/** ProductController end **/
Route::get('shippings', [CheckoutController::class, 'shippingsAndPaymentMethods']);
Route::post('successPayment/{type}',[\FleetCart\Http\Controllers\CreditCartSubmitController::class, 'successPayment']);
Route::post('errorPayment',[\FleetCart\Http\Controllers\CreditCartSubmitController::class, 'errorPayment']);
Route::get('blog/{id}',[\FleetCart\Http\Controllers\BlogController::class,'findById']);
Route::get('blogs',[\FleetCart\Http\Controllers\BlogController::class,'index']);

/** Param Pos Controller **/
Route::get('paymentCreditCart/{type}',[\FleetCart\Http\Controllers\CreditCartSubmitController::class, 'index']);
Route::post('getCartType',[\FleetCart\Http\Controllers\CreditCartSubmitController::class, 'getCardInformations']);
Route::post('kargo/test',[\FleetCart\Http\Controllers\KargoController::class, 'CreateShipment']);
/** Param Post Controller end */



Route::post('basket/storeAll',[BasketController::class,'storeAll']);

/** review */
Route::resource('review', ReviewController::class)->only('index', 'store');
/** review end */

/** Auth Controller */
Route::post('register', [AuthController::class, 'register']);
/** Auth Controller end **/

/** Category Controller */
Route::resource('categories', CategoryController::class)->only('index');
/** CategoryController end **/



// token middleware
Route::group(['middleware' => JWTMiddleware::class], function () {


    /** User Controller */
    Route::post('users/{user}/update', [UserController::class, 'update']);
    Route::post('users/{user}/updatePassword', [UserController::class, 'updatePassword']);
    Route::get('users/recentOrders', [UserController::class, 'recentOrders']);
    Route::get('users/orders', [UserController::class, 'orders']);
    /** User controller end **/


    /** Address Controller */
    Route::post('addresses', [AddressController::class, 'store']);
    Route::get('addresses', [AddressController::class, 'index']);

    Route::put('addresses/{address}', [AddressController::class, 'update']);
    Route::delete('addresses/{address}', [AddressController::class, 'delete']);
    Route::get('addresses/{address}', [AddressController::class, 'show']);
    /** Address Controller end */


    /** Wish List */
    Route::resource('wishlists', WishListController::class)->only('index', 'store');
    Route::delete('wishlists/{id}', [WishListController::class, 'destroy']);
    /** Wish List End */

    /** basket */

    Route::post('basket', [BasketController::class, 'store']);
    Route::get('basket', [BasketController::class, 'index']);
    Route::delete('basket/{basket}', [BasketController::class, 'delete']);
    Route::post('updateBasketQuantity/{basketId}', [BasketController::class, 'updateBasketQuantity']);
    Route::get('getBasketForCreditCard/{id}', [BasketController::class, 'getBasketForCreditCard']);
    /** basket end */

    /** checkout */
    Route::post('checkout', [CheckoutController::class, 'store']);
    Route::post('checkout-credit', [CheckoutController::class, 'createOrderSnapshot']);
    Route::post('couponExists', [CheckoutController::class, 'couponExists']);
    /** checkout end */

    /** Param Controller */
    Route::post('pay-credit/{type}', [\FleetCart\Http\Controllers\CreditCartSubmitController::class, 'checkout']);
    /** Param Controller end */

    Route::get('my-orders',[\FleetCart\Http\Controllers\OrderController::class,'index']);
    Route::get('findOrderById/{id}',[\FleetCart\Http\Controllers\OrderController::class,'findById']);


    /** Refund Controller */
    Route::get('refunds',[\FleetCart\Http\Controllers\RefundController::class,'index']);
    Route::post('refunds',[\FleetCart\Http\Controllers\RefundController::class,'store']);
    /** Refund Controller end */

});


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
