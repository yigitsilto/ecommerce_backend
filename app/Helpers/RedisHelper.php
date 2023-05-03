<?php

namespace FleetCart\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisHelper
{

    public static function redisClear()
    {
        Redis::del('data');
    }

}