<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Blog;
use FleetCart\Services\AddressService;
use Illuminate\Http\Request;
use Modules\Address\Entities\Address;

class BlogController extends Controller
{
    public function index(){
        return response()->json(Blog::query()->paginate(12));
    }
}
