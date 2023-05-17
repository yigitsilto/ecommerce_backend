<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Filter;

class FiltersController extends Controller
{
    public function index()
    {
        $filters = Filter::query()
                         ->with('values')
                         ->get();

        return response()->json($filters);

    }
}
