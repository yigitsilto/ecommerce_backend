<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Blog;

class BlogController extends Controller
{
    public function findById($id)
    {
        return response()->json(Blog::query()
                                    ->where('id', $id)
                                    ->first());
    }

    public function index()
    {
        return response()->json(Blog::query()
                                    ->paginate(10));
    }
}
