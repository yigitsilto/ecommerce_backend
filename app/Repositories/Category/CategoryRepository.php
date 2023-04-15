<?php
namespace FleetCart\Repositories\Category;
use Modules\Category\Entities\Category;

class CategoryRepository implements CategoryRepositoryInterface
{

    public function index()
    {
        return Category::query()->get()->nest();
    }
}
