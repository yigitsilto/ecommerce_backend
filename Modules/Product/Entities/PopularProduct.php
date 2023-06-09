<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Modules\User\Entities\CompanyPrice;

class PopularProduct extends Model
{

    protected $guarded = [];



    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}