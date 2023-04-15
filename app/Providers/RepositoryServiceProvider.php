<?php

namespace FleetCart\Providers;


use FleetCart\Repositories\Category\CategoryRepository;
use FleetCart\Repositories\Category\CategoryRepositoryInterface;
use FleetCart\Repositories\Product\ProductRepository;
use FleetCart\Repositories\Product\ProductRepositoryInterface;
use FleetCart\Repositories\Address\AddressRepository;
use FleetCart\Repositories\Address\AddressRepositoryInterface;
use FleetCart\Services\CardTypeBinApiService;
use FleetCart\Services\CardTypeBinApiServiceImpl;
use FleetCart\Services\CheckoutService;
use FleetCart\Services\CheckoutServiceImpl;
use FleetCart\Services\ParamPosService;
use FleetCart\Services\ParamPosServiceImpl;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductRepositoryInterface::class,ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,CategoryRepository::class);
        $this->app->bind(AddressRepositoryInterface::class,AddressRepository::class);
        $this->app->bind(CheckoutService::class,CheckoutServiceImpl::class);
        $this->app->bind(ParamPosService::class, ParamPosServiceImpl::class);
        $this->app->bind(CardTypeBinApiService::class,CardTypeBinApiServiceImpl::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
