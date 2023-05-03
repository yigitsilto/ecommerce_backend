<?php

namespace FleetCart\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisHelper
{

    public static function redisClear()
    {
        Redis::del('products');
        Redis::del('sliders');
        Redis::del('blogs');
        Redis::del('popularCategories');
        Redis::del('brands');
    }

}