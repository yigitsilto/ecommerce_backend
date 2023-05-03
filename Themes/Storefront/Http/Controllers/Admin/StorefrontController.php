<?php

namespace Themes\Storefront\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Modules\Admin\Ui\Facades\TabManager;
use Themes\Storefront\Banner;
use Themes\Storefront\Http\Requests\SaveStorefrontRequest;

class StorefrontController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $settings = setting()->all();
        $tabs = TabManager::get('storefront');

        return view('admin.storefront.edit', compact('settings', 'tabs'));
    }

    private function redisUpdate(){
        Redis::del('products');
        Redis::del('settings');
        Redis::del('sliders');
        Redis::del('categoryWithProducts');
        Redis::del('popularCategories');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SaveStorefrontRequest $request)
    {
        $this->redisUpdate();
        setting($request->except('_token', '_method'));

        return back()->withSuccess(trans('admin::messages.resource_saved', ['resource' => trans('setting::settings.settings')]));
    }

    public function delete(Request  $request){
        $this->redisUpdate();
         Banner::deleteByName($request->banner);
         return 1;
    }
}
