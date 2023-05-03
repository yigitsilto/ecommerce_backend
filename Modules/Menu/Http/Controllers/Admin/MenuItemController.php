<?php

namespace Modules\Menu\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redis;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Menu\Entities\MenuItem;
use Modules\Menu\Http\Requests\SaveMenuItemRequest;

class MenuItemController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param int $menuId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($menuId, SaveMenuItemRequest $request)
    {

        Redis::del('products');
        Redis::del('settings');
        Redis::del('sliders');
        Redis::del('categoryWithProducts');
        Redis::del('brands');
        Redis::del('popularCategories');
        $menuItem = MenuItem::create(
            $this->prepare($menuId, $request->all())
        );

        return redirect()
            ->route('admin.menus.edit', $menuId)
            ->withSuccess(trans('admin::messages.resource_saved', ['resource' => trans('menu::menu_items.menu_item')]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $menuId
     * @return \Illuminate\Http\Response
     */
    public function create($menuId)
    {
        $menuItem = new MenuItem;
        $tabs = TabManager::get('menu_items');

        return view('menu::admin.menu_items.create', compact('menuId', 'menuItem', 'tabs'));
    }

    /**
     * Prepare menu item attributes for saving.
     *
     * @param int $menuId
     * @param array $attributes
     * @return array
     */
    private function prepare($menuId, array $attributes)
    {
        if (is_null($attributes['parent_id'])) {
            $attributes['parent_id'] = $this->parentId($menuId);
        }

        return array_merge($attributes, ['menu_id' => $menuId]);
    }

    /**
     * Get parent id for the given menu id.
     *
     * @param string string
     * @return int
     */
    private function parentId($menuId)
    {
        return MenuItem::withoutGlobalScope('not_root')
                       ->where('menu_id', $menuId)
                       ->value('id');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $menuId
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($menuId, $id)
    {
        $menuItem = MenuItem::withoutGlobalScope('active')
                            ->findOrFail($id);
        $tabs = TabManager::get('menu_items');

        return view('menu::admin.menu_items.edit', compact('menuId', 'menuItem', 'tabs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $menuId
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($menuId, $id, SaveMenuItemRequest $request)
    {
        Redis::del('products');
        Redis::del('settings');
        Redis::del('sliders');
        Redis::del('categoryWithProducts');
        Redis::del('brands');
        Redis::del('popularCategories');
        MenuItem::withoutGlobalScope('active')
                ->findOrFail($id)
                ->update(
                    $this->prepare($menuId, $request->all())
                );

        return redirect()
            ->route('admin.menus.edit', $menuId)
            ->withSuccess(trans('admin::messages.resource_saved', ['resource' => trans('menu::menu_items.menu_item')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        MenuItem::withoutGlobalScope('active')
                ->findOrFail($id)
                ->delete();
    }
}
