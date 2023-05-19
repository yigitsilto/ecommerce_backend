<?php

namespace Modules\Product\Http\Controllers\Admin;

use FleetCart\Filter;
use FleetCart\FilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FilterController
{

    public function index()
    {

        $filters = Filter::query()
                         ->orderBy("id")
                         ->paginate(15);

        return view('product::admin.products.filters.index')->with(compact('filters'));
    }

    public function edit($id)
    {
        $filter = Filter::query()
                        ->findOrFail($id);

        return view('product::admin.products.filters.update')->with(compact('filter'));
    }

    public function delete($id)
    {

        // do passive
        $filter = Filter::query()
                        ->findOrFail($id);

        $filter->status = !$filter->status;
        $filter->save();
        return redirect()->route('admin.filters.index');

    }

    public function update(Request $request, $id)
    {


        $request->validate([
                               'title' => 'required',
                               'values' => 'required|array',
                               'values.*.title' => 'required|string',
                               'values.*.id' => 'required|exists:filter_values,id',
                           ]);


        // update
        $filter = Filter::query()
                        ->findOrFail($id);

        $filter->title = $request->input('title');
        $filter->slug = Str::slug($request->input('title'));
        $filter->save();


        // insert new values
        foreach ($request->input('values') as $item) {
            FilterValue::query()
                       ->where('id', $item['id'])
                       ->update([
                                    'title' => $item['title'],
                                    'slug' => Str::slug($item['title']),
                                ]);
        }

        if ($request->has('newValues')) {

            foreach ($request->input('newValues') as $item) {
                FilterValue::query()
                           ->create([
                                        'filter_id' => $filter->id,
                                        'title' => $item,
                                        'slug' => Str::slug($item),
                                    ]);
            }

        }


        return redirect()->route('admin.filters.index');


    }

    public function create()
    {
        return view('product::admin.products.filters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
                               'title' => 'required',
                               'values' => 'required|array',
                               'values.*' => 'required|string',
                           ]);

        try {


            DB::beginTransaction();

            $filter = Filter::query()
                            ->create([
                                         'title' => $request->input('title'),
                                         'slug' => Str::slug($request->input('title')),
                                     ]);
            foreach ($request->input('values') as $item) {
                FilterValue::query()
                           ->create([
                                        'filter_id' => $filter->id,
                                        'title' => $item,
                                        'slug' => Str::slug($item),
                                    ]);
            }


            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
        }

        return redirect()->route('admin.filters.index');


    }

    public function deleteValue($id){
dd(2);
        $value = FilterValue::query()
                            ->findOrFail($id);
        $value->delete();
        return redirect()->back();
    }
}
