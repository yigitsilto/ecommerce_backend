<?php

use Illuminate\Support\Facades\Route;

Route::get('products', [
    'as' => 'admin.products.index',
    'uses' => 'ProductController@index',
    'middleware' => 'can:admin.products.index',
]);

Route::get('products/create', [
    'as' => 'admin.products.create',
    'uses' => 'ProductController@create',
    'middleware' => 'can:admin.products.create',
]);

Route::post('products', [
    'as' => 'admin.products.store',
    'uses' => 'ProductController@store',
    'middleware' => 'can:admin.products.create',
]);

Route::get('products/{id}/edit', [
    'as' => 'admin.products.edit',
    'uses' => 'ProductController@edit',
    'middleware' => 'can:admin.products.edit',
]);

Route::put('products/{id}', [
    'as' => 'admin.products.update',
    'uses' => 'ProductController@update',
    'middleware' => 'can:admin.products.edit',
]);

Route::delete('products/{ids}', [
    'as' => 'admin.products.destroy',
    'uses' => 'ProductController@destroy',
    'middleware' => 'can:admin.products.destroy',
]);


Route::get('filters', [
    'as' => 'admin.filters.index',
    'uses' => 'FilterController@index',
    'middleware' => 'can:admin.products.edit',
]);

Route::get('filters/create', [
    'as' => 'admin.filters.create',
    'uses' => 'FilterController@create',
    'middleware' => 'can:admin.products.create',
]);

Route::post('filters', [
    'as' => 'admin.filters.store',
    'uses' => 'FilterController@store',
]);

Route::get('filters/{id}', [
    'as' => 'admin.filters.edit',
    'uses' => 'FilterController@edit',
]);

Route::put('filters/{id}', [
    'as' => 'admin.filters.update',
    'uses' => 'FilterController@update',
]);

Route::delete('filters/{id}', [
    'as' => 'admin.filters.delete',
    'uses' => 'FilterController@delete',
]);


Route::delete('filters/value/{id}', [
    'as' => 'admin.filters.value.delete',
    'uses' => 'FilterController@deleteValue',
]);

Route::get('popular-products', [
    'as' => 'admin.popularProducts.index',
    'uses' => 'PopularProductsController@index',
]);

Route::post('popular-products', [
    'as' => 'admin.popularProducts.store',
    'uses' => 'PopularProductsController@store',
]);