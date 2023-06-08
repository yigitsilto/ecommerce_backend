<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Filter;
use Illuminate\Http\Request;
use Modules\Category\Entities\Category;

class FiltersController extends Controller
{
    public function index(Request $request)
    {
        $allFilters = Filter::query()
                         ->with('values')
                         ->get();

        $filterArray = [];
        $filterValues = [];
        $filterMap = [];

        foreach ($allFilters as $item) {
            $filterArray[] = ['id' => $item->id, 'title' => $item->title, 'slug' => $item->slug];
            $filterMap[$item->id] = &$filterArray[count($filterArray) - 1];

            foreach ($item->values as $value) {
                if ($item->slug == 'kategoriler') {
                    $category = Category::where('slug', $request->get('category'))->first();
                    $categoryId = $category->id;

                    $ca = Category::where('slug', $value->slug)->first();

                    if ($categoryId == $ca->parent_id) {
                        $filterValues[] = [
                            'id' => $value->id,
                            'title' => $value->title,
                            'slug' => $value->slug,
                            'filter_id' => $value->filter_id
                        ];
                    } else {
                        continue;
                    }
                }

                if (isset($filterMap[$value->filter_id])) {
                    $filterMap[$value->filter_id]['values'][] = &$filterValues[count($filterValues) - 1];
                }
            }
        }

        return response()->json($filterArray);

    }
}
