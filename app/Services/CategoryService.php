<?php

namespace FleetCart\Services;

use FleetCart\Repositories\Category\CategoryRepositoryInterface;

class CategoryService
{

    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(){
        return $this->categoryRepository->index();
    }
}
