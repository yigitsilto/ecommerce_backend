<?php

namespace Modules\Admin\Traits;

use FleetCart\FilterValue;
use FleetCart\Helpers\RedisHelper;
use FleetCart\ProductFilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Media\Entities\File;
use Modules\Product\Entities\EntityFiles;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductPrice;
use Modules\Product\Http\Requests\SaveProductRequest;
use Modules\Support\Search\Searchable;
use Modules\Tax\Entities\TaxRate;

trait HasCrudActions
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('query')) {
            return $this->getModel()
                        ->search($request->get('query'))
                        ->query()
                        ->limit($request->get('limit', 10))
                        ->get();
        }

        if ($request->has('table')) {
            return $this->getModel()
                        ->table($request);
        }


        return view("{$this->viewPath}.index");
    }

    /**
     * Get a new instance of the model.
     *
     * @return \Modules\Support\Eloquent\Model
     */
    protected function getModel()
    {
        return new $this->model;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $this->disableSearchSyncing();


        $request = $this->getRequest('store');

        dd($request->all());

        if ($request instanceof SaveProductRequest) {


            $request = $this->getRequest('store')
                            ->merge([
                                        'price' => $this->getRequest('store')
                                                        ->all()['prices'][1]
                                    ]);
        }

        $entity = $this->getModel()
                       ->create(
                           $request->all()
                       );

        if ($entity instanceof Product) {

            foreach ($request->all()['prices'] as $index => $price) {


                ProductPrice::query()
                            ->updateOrCreate(
                                [
                                    'product_id' => $entity->id,
                                    'company_price_id' => $index,
                                ],
                                [
                                    'product_id' => $entity->id,
                                    'price' => $price,
                                    'company_price_id' => $index
                                ]);
            }

            Product::query()
                   ->where('id', $entity->id)
                   ->update(['tax' => 20]);

        }


        $this->searchable($entity);

        RedisHelper::redisClear();

        if ($entity instanceof Product) {


            if (isset($this->getRequest('store')
                           ->all()['filter_values'])) {
                $this->createFilterValues($this->getRequest('store')
                                               ->all()['filter_values'], $entity);
            }


        }


        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo($entity);
        }

        return redirect()
            ->route("{$this->getRoutePrefix()}.index")
            ->withSuccess(trans('admin::messages.resource_saved', ['resource' => $this->getLabel()]));
    }

    /**
     * Disable search syncing for the entity.
     *
     * @return void
     */
    protected function disableSearchSyncing()
    {
        if ($this->isSearchable()) {
            $this->getModel()
                 ->disableSearchSyncing();
        }
    }

    /**
     * Determine if the entity is searchable.
     *
     * @return bool
     */
    protected function isSearchable()
    {
        return in_array(Searchable::class, class_uses_recursive($this->getModel()));
    }

    /**
     * Get request object
     *
     * @param string $action
     * @return \Illuminate\Http\Request
     */
    protected function getRequest($action)
    {
        if (!isset($this->validation)) {
            return request();
        }

        if (isset($this->validation[$action])) {
            return resolve($this->validation[$action]);
        }

        return resolve($this->validation);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array_merge([
                                'tabs' => TabManager::get($this->getModel()
                                                               ->getTable()),
                                $this->getResourceName() => $this->getModel(),
                            ], $this->getFormData('create'));

        return view("{$this->viewPath}.create", $data);
    }

    /**
     * Get name of the resource.
     *
     * @return string
     */
    protected function getResourceName()
    {
        if (isset($this->resourceName)) {
            return $this->resourceName;
        }

        return lcfirst(class_basename($this->model));
    }

    /**
     * Get form data for the given action.
     *
     * @param string $action
     * @param mixed ...$args
     * @return array
     */
    protected function getFormData($action, ...$args)
    {
        if (method_exists($this, 'formData')) {
            return $this->formData(...$args);
        }

        if ($action === 'create' && method_exists($this, 'createFormData')) {
            return $this->createFormData();
        }

        if ($action === 'edit' && method_exists($this, 'editFormData')) {
            return $this->editFormData(...$args);
        }

        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {


        $entity = $this->getEntity($id);

        $request = $this->getRequest('update');


        if ($entity instanceof Product) {

            $request = $this->getRequest('update')
                            ->merge([
                                        'price' => $this->getRequest('update')
                                                        ->all()['prices'][1]
                                    ]);


            foreach ($request->all()['prices'] as $index => $price) {

                $formattedNumber = number_format((float)$price, 4, '.', '');

                ProductPrice::query()
                            ->updateOrCreate(
                                [
                                    'product_id' => $id,
                                    'company_price_id' => $index,
                                ],
                                [
                                    'product_id' => $id,
                                    'price' => $price,
                                    'company_price_id' => $index
                                ]);
            }


        }

        $this->disableSearchSyncing();

        $entity->update(
            $request->all()
        );


        if ($entity instanceof Product) {


            if ($request->has('tax_class_id') && !is_null($request->get('tax_class_id'))){

                $taxRate = TaxRate::query()
                                  ->where('tax_class_id', $request->get("tax_class_id"))
                                  ->first();
                if ($taxRate) {
                    Product::query()->where('id', $entity->id)
                           ->update(['tax' => $taxRate->rate]);
                }
            }


            $data = $this->getRequest('update')
                         ->all();

            $entityAfterSaved = $this->getEntity($id);

            $this->insertOptionValueImage($entityAfterSaved, $data);
            if (isset($data['filter_values'])) {
                $this->createFilterValues($data['filter_values'], $entityAfterSaved);

            }


        }


        $this->searchable($entity);

        RedisHelper::redisClear();

        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo($entity)
                        ->withSuccess(trans('admin::messages.resource_saved', ['resource' => $this->getLabel()]));
        }


        return redirect()
            ->route("{$this->getRoutePrefix()}.index")
            ->withSuccess(trans('admin::messages.resource_saved', ['resource' => $this->getLabel()]));
    }

    /**
     * Get an entity by the given id.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getEntity($id)
    {
        return $this->getModel()
                    ->with($this->relations())
                    ->withoutGlobalScope('active')
                    ->findOrFail($id);
    }

    /**
     * Get the relations that should be eager loaded.
     *
     * @return array
     */
    private function relations()
    {
        return collect($this->with ?? [])
            ->mapWithKeys(function ($relation) {
                return [
                    $relation => function ($query) {
                        return $query->withoutGlobalScope('active');
                    }
                ];
            })
            ->all();
    }

    /**
     * @param $entityAfterSaved
     * @param $data
     * @return void
     * varyasyonların resimlerini günceller
     */
    private function insertOptionValueImage($entityAfterSaved, $data)
    {
        foreach ($entityAfterSaved->options as $option) {
            foreach ($option->values as $value) {
                if (isset($value->image)) {
                    $optionIndex = array_search($option->id, array_column($data['options'], 'id'));
                    if ($optionIndex !== false && isset($data['options'][$optionIndex]['values'])) {
                        $valueIndex = array_search($value->id,
                                                   array_column($data['options'][$optionIndex]['values'], 'id'));

                        if ($valueIndex !== false && isset($data['options'][$optionIndex]['values'][$valueIndex]['image'])) {

                            $image = $data['options'][$optionIndex]['values'][$valueIndex]['image'];

                            $path = Storage::putFile('media', $image);

                            $file = File::create([
                                                     'user_id' => auth()->id(),
                                                     'disk' => config('filesystems.default'),
                                                     'filename' => $image->getClientOriginalName(),
                                                     'path' => $path,
                                                     'extension' => $image->guessClientExtension() ?? '',
                                                     'mime' => $image->getClientMimeType(),
                                                     'size' => $image->getSize(),
                                                 ]);

                            $value->update([
                                               'image' => $file->path
                                           ]);

                            $valueData = $value->fresh();

                            EntityFiles::query()
                                       ->updateOrCreate([
                                                            'entity_id' => $valueData->id,
                                                            'entity_type' => 'FleetCart\OptionValue',
                                                            'file_id' => $file->id,
                                                            'zone' => 'base_image'
                                                        ]);
                        }
                    }
                }

            }
        }
    }

    /**
     * @param $arrayValues
     * @param $entityAfterSaved
     * @return void
     * Filtre değerlerine göre kayıt oluşturur.
     */
    private function createFilterValues($arrayValues, $entityAfterSaved)
    {

        foreach ($arrayValues as $value) {
            $filterValue = FilterValue::query()
                                      ->where('id', $value)
                                      ->first();

            ProductFilterValue::query()
                              ->firstOrCreate([
                                                  'filter_id' => $filterValue->filter_id,
                                                  'filter_value_id' => $filterValue->id,
                                                  'product_id' => $entityAfterSaved->id
                                              ],
                                              [
                                                  'filter_id' => $filterValue->filter_id,
                                                  'filter_value_id' => $filterValue->id,
                                                  'product_id' => $entityAfterSaved->id
                                              ]);
        }
    }

    /**
     * Make the given model instance searchable.
     *
     * @return void
     */
    protected function searchable($entity)
    {
        if ($this->isSearchable($entity)) {
            $entity->searchable();
        }
    }

    /**
     * Get label of the resource.
     *
     * @return void
     */
    protected function getLabel()
    {
        return trans($this->label);
    }

    /**
     * Get route prefix of the resource.
     *
     * @return string
     */
    protected function getRoutePrefix()
    {
        if (isset($this->routePrefix)) {
            return $this->routePrefix;
        }

        return "admin.{$this->getModel()->getTable()}";
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = $this->getEntity($id);

        if (request()->wantsJson()) {
            return $entity;
        }

        return view("{$this->viewPath}.show")->with($this->getResourceName(), $entity);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = array_merge([
                                'tabs' => TabManager::get($this->getModel()
                                                               ->getTable()),
                                $this->getResourceName() => $this->getEntity($id),
                            ], $this->getFormData('edit', $id));

        return view("{$this->viewPath}.edit", $data);
    }

    /**
     * Destroy resources by given ids.
     *
     * @param string $ids
     * @return void
     */
    public function destroy($ids)
    {
        $this->getModel()
             ->withoutGlobalScope('active')
             ->whereIn('id', explode(',', $ids))
             ->delete();
    }
}
