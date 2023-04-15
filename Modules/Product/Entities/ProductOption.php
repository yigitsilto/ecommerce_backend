<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;

class ProductOption extends Model
{

    public $timestamps = false;

    protected $table = 'product_options';

    protected $guarded = [];


}
