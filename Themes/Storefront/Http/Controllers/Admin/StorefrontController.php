<?php

namespace Themes\Storefront\Http\Controllers\Admin;

use FleetCart\Helpers\RedisHelper;
use FleetCart\StoreFrontSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Media\Entities\File;
use Modules\Product\Entities\EntityFiles;
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
        RedisHelper::redisClear();
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
        try {
            StoreFrontSetting::where('id', '>', 0)->delete();
            foreach (setting()->all() as $keyForSettings => $value) {
                // save the StorFront settings to table
                $valueForCreate = is_array($value) ? json_encode($value) : $value;
                $isForeignId = is_numeric($value) && $value != "0" ? true : false;
                \FleetCart\StoreFrontSetting::create(
                    [
                        'key' => $keyForSettings,
                        'value' => $valueForCreate,
                        'is_foreign_id' => $isForeignId
                    ],
                );

            }

            $logo = $this->getMedia(setting('storefront_header_logo'))->path;
            $favicon = $this->getMedia(setting('storefront_favicon'))->path;

            StoreFrontSetting::query()->where('key', 'storefront_header_logo')->update([
                'value' => $logo,
                'is_foreign_id' => false
            ]);
            StoreFrontSetting::query()->where('key', 'storefront_favicon')->update([
                'value' => $favicon,
                'is_foreign_id' => false
            ]);

        } catch (\Exception $e) {

        }

        return back()->withSuccess(trans('admin::messages.resource_saved', ['resource' => trans('setting::settings.settings')]));
    }

    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }

    public function delete(Request  $request){
        $this->redisUpdate();
         Banner::deleteByName($request->banner);
         return 1;
    }
}
