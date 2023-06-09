@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', "Popüler Ürünler")

@endcomponent

@component('admin::components.page.index_table')
    @slot('name', "Popüler Ürünler")

@section('content')
    <div class="box box-default">
        <div class="box-body clearfix">

            <div class="col-lg-12 col-md-12">
                <div class="tab-wrapper category-details-tab">

                    <form method="POST" action="{{ route('admin.popularProducts.store') }}" class="form-horizontal"
                          id="create-form" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="tab-content">
                            <div id="general-information" class="tab-pane fade in active">

                                <div class="row">
                                    <div class="col-md-8" style="height: 300px;">
                                        {{ Form::select('id', "Popüler Ürünler", $errors, $products, $popularProducts, ['class' => 'selectize prevent-creation', 'multiple' => true]) }}


                                    </div>



                                </div>

                                <button type="submit" class="btn btn-primary mt-4" style="margin-top: 12px"
                                >
                                    {{ trans('admin::admin.buttons.save') }}
                                </button>


                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection


@endcomponent

