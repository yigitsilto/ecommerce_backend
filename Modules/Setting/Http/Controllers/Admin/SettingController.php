<?php

namespace Modules\Setting\Http\Controllers\Admin;

use FleetCart\Helpers\RedisHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Core\Http\Requests\Request;
use Modules\Setting\Entities\ShippingCompany;
use Modules\Setting\Http\Requests\UpdateSettingRequest;

class SettingController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $settings = setting()->all();
        $tabs = TabManager::get('settings');

        return view('setting::admin.settings.edit', compact('settings', 'tabs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request)
    {
        if ($request->has('company_name')){
            ShippingCompany::query()->updateOrCreate([
                'name' => $request->get('company_name')
            ],[
                'name' => $request->get('company_name'),
                'price' => $request->get('company_price'),
                'status' => $request->get('company_status'),

            ]);
        }


        $this->redisUpdate();

        $this->handleMaintenanceMode($request);

        setting($request->except('_token', '_method'));

        return redirect(non_localized_url())
            ->with('success', trans('setting::messages.settings_have_been_saved'));
    }

    private function redisUpdate(){
        RedisHelper::redisClear();
    }

    private function handleMaintenanceMode($request)
    {
        $this->redisUpdate();
        if ($request->maintenance_mode) {
            Artisan::call('down');
        } elseif (app()->isDownForMaintenance()) {
            Artisan::call('up');
        }
    }

    public function companiesCreate(){
        return view('setting::admin.settings.shippingTypeCreate');

    }

    public function createCompany(\Illuminate\Http\Request $request){
         ShippingCompany::query()->updateOrCreate([
            'name' => $request->get('name')
        ],[
            'name' => $request->get('name'),
            'price' => $request->get('price'),
        ]);
        $this->redisUpdate();
         return redirect()->route('admin.settings.companies');
    }

    public function companies(){
        $company = ShippingCompany::all();
        return view('setting::admin.settings.shippingTypes')->with(compact('company'));
    }

    public function deleteCompany($id){
        $this->redisUpdate();
        ShippingCompany::query()->find($id)->delete();
        return redirect()->back();

    }

}
