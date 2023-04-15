@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>Kargo Firmaları</h3>

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

                    <form method="POST" action="{{ route('admin.settings.createCompany') }}" class="form-horizontal"
                          id="category-form" >
                        {{ csrf_field() }}

                        <div class="tab-content">
                            <div id="general-information" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-md-6">

                                        <label for="">Firma İsmi</label>
                                        <input type="text" required name="name" placeholder="Firma ismi"
                                               class="form-control">

                                    </div>

                                    <div class="col-md-6">

                                        <label for="">Firma Kargo Fiyat</label>
                                        <input type="number" required name="price" placeholder="Fiyat"
                                               class="form-control">

                                    </div>
                                </div>

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
