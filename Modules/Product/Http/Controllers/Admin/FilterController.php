<?php

namespace Modules\Product\Http\Controllers\Admin;

use Modules\Admin\Traits\HasCrudActions;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\SaveProductRequest;

class FilterController
{

    public function index(){


        return view('product::admin.products.filters.index');
    }
}
