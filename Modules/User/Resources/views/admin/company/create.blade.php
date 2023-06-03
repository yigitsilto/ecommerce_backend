@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>Bayi/Üye Grup Ekle</h3>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('setting::settings.settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="box box-default">
        <div class="box-body clearfix">

            <div class="col-lg-12 col-md-12">
                <div class="tab-wrapper category-details-tab">

                    <form method="POST" action="{{ route('admin.company.store') }}" class="form-horizontal" id="product-create-form" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="tab-content">
                            <div id="general-information" class="tab-pane fade in active">

                                {{ Form::text('title', 'Başlık', $errors, 'title', ['required' => true]) }}

                                {{ Form::select('companyPrice', 'Fiyat Türü', $errors, $companyPrices, [ 'required' => true]) }}

                                <button type="submit" class="btn btn-primary mt-4" style="margin-top: 10px"
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
