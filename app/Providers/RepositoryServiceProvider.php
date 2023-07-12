<?php

namespace FleetCart\Providers;


use FleetCart\Repositories\Category\CategoryRepository;
use FleetCart\Repositories\Category\CategoryRepositoryInterface;
use FleetCart\Repositories\Product\ProductRepository;
use FleetCart\Repositories\Product\ProductRepositoryInterface;
use FleetCart\Repositories\Address\AddressRepository;
use FleetCart\Repositories\Address\AddressRepositoryInterface;
use FleetCart\Services\BasketService;
use FleetCart\Services\BasketServiceImpl;
use FleetCart\Services\CardTypeBinApiService;
use FleetCart\Services\CardTypeBinApiServiceImpl;
use FleetCart\Services\CheckoutService;
use FleetCart\Services\CheckoutServiceImpl;
use FleetCart\Services\CreditCartSubmitService;
use FleetCart\Services\CreditCartSubmitServiceImpl;
use FleetCart\Services\ParamPosService;
use FleetCart\Services\ParamPosServiceImpl;
use FleetCart\Services\RefundService;
use FleetCart\Services\RefundServiceImpl;
use FleetCart\Services\ZiraatService;
use FleetCart\Services\ZiraatServiceImpl;
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
        $this->app->bind(BasketService::class,BasketServiceImpl::class);
        $this->app->bind(RefundService::class,RefundServiceImpl::class);
        $this->app->bind(CreditCartSubmitService::class,CreditCartSubmitServiceImpl::class);
        $this->app->bind(ZiraatService::class,ZiraatServiceImpl::class);

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
