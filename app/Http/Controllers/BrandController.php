<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Resources\BannerResource;
use Illuminate\Http\Request;
use Modules\Brand\Entities\Brand;

class BrandController extends Controller
{
    public function index(){
        $brands = BannerResource::collection(Brand::query()->where('is_active',true)->paginate(12));

        return response()->json($brands);
    }
}
