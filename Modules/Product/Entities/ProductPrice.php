<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Modules\User\Entities\CompanyPrice;

class ProductPrice extends Model
{

    protected $guarded = [];



    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function companyPrice()
    {
        return $this->belongsTo(CompanyPrice::class);
    }

}