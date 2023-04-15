<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Resources\CategoryResource;
use FleetCart\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryService;
    public function __construct(CategoryService  $categoryService){
        $this->categoryService = $categoryService;
    }
    public function index(){
        return $this->categoryService->index();
    }
}
