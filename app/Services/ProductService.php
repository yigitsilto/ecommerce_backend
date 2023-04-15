<?php
namespace FleetCart\Services;

use FleetCart\Repositories\Product\ProductRepositoryInterface;
use Modules\Product\Entities\Product;

class ProductService
{
    private $productRepository ;

    public function __construct(ProductRepositoryInterface  $productRepository){
        $this->productRepository=$productRepository;
    }
    public function index(){
        return $this->productRepository->index();
    }
    public function show(int $id){
        return $this->productRepository->show($id);
    }
}
