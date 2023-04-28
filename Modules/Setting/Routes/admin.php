<?php

use Illuminate\Support\Facades\Route;

Route::get('settings', [
    'as' => 'admin.settings.edit',
    'uses' => 'SettingController@edit',
    'middleware' => 'can:admin.settings.edit',
]);

Route::put('settings', [
    'as' => 'admin.settings.update',
    'uses' => 'SettingController@update',
    'middleware' => 'can:admin.settings.edit',
]);


Route::post('company-create', [
    'as' => 'admin.settings.createCompany',
    'uses' => 'SettingController@createCompany',
    'middleware' => 'can:admin.settings.edit',
]);

Route::get('settings/companies', [
    'as' => 'admin.settings.companies',
    'uses' => 'SettingController@companies',
    'middleware' => 'can:admin.settings.edit',
]);

Route::get('settings/companies/create', [
    'as' => 'admin.settings.companies.create',
    'uses' => 'SettingController@companiesCreate',
    'middleware' => 'can:admin.settings.edit',
]);

Route::post('company-delete/{id}', [
    'as' => 'admin.settings.deleteCompany',
    'uses' => 'SettingController@deleteCompany',
    'middleware' => 'can:admin.settings.edit',
]);


Route::get('blogs', [
    'as' => 'admin.blogs',
    'uses' => 'BlogController@index',
    'middleware' => 'can:admin.settings.edit',
]);


Route::get('blogs/create', [
    'as' => 'admin.blogs.create',
    'uses' => 'BlogController@create',
    'middleware' => 'can:admin.settings.edit',
]);

Route::post('blogs', [
    'as' => 'admin.blogs.store',
    'uses' => 'BlogController@store',
    'middleware' => 'can:admin.settings.edit',
]);

Route::get('blogs/{id}', [
    'as' => 'admin.blogs.edit',
    'uses' => 'BlogController@edit',
    'middleware' => 'can:admin.settings.edit',
]);

Route::post('blogs/{id}', [
    'as' => 'admin.blogs.update',
    'uses' => 'BlogController@update',
    'middleware' => 'can:admin.settings.edit',
]);

Route::post('blogs/{id}/delete', [
    'as' => 'admin.blogs.delete',
    'uses' => 'BlogController@delete',
    'middleware' => 'can:admin.settings.edit',
]);

