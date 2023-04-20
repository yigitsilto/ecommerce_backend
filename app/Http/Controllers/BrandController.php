<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Resources\BannerResource;
use Illuminate\Support\Facades\Redis;
use Modules\Brand\Entities\Brand;

class BrandController extends Controller
{
    public function index()
    {

        $brands = unserialize(Redis::get('brands'));

        if (!$brands) {
            $brands = Brand::query()
                           ->where('is_active', true)
                           ->paginate(12);
            Redis::set('brands', serialize($brands));
        }


        return response()->json(BannerResource::collection($brands));
    }
}
