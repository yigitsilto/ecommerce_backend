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
