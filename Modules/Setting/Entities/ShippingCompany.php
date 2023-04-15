<?php

namespace Modules\Setting\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Support\Money;

class ShippingCompany extends Model
{

    protected $table = 'shipping_companies';

    protected $guarded = [];


    public function getPriceAttribute($price)
    {
        return Money::inDefaultCurrency($price);
    }


}
