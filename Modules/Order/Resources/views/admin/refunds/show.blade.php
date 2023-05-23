@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>İade Talebi</h3>

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

                    <form method="POST" action="{{ route('admin.refunds.status.update', $refund->id) }}"
                          class="form-horizontal"
                          id="product-create-form">
                        {{ csrf_field() }}
                        {{ method_field('put') }}

                        <div class="tab-content">
                            <div id="general-information" class="tab-pane fade in active">

                                {{ Form::text('product.name', 'Ürün', $errors, $refund, ['labelCol' => 2, 'required' => true,'disabled' => true]) }}

                                {{ Form::text('user.full_name', 'Kullanıcı', $errors, $refund, ['labelCol' => 2, 'required' => true,'disabled' => true]) }}


                                {{ Form::textarea('reason', 'Açıklama', $errors, $refund, ['labelCol' => 2, 'required' => true,'disabled' => true]) }}

                                <label for="ss">Durum * </label>

                                <select name="status" id="ss" class="form-control">

                                    @if($refund->status == 'PENDING')

                                        <option selected value="{{$refund->status}}">Bekliyor</option>

                                    @elseif($refund->status == 'SUCCESS')

                                        <option selected value="{{$refund->status}}">Tamamlandı</option>

                                    @elseif($refund->status == 'REJECTED')

                                        <option selected value="{{$refund->status}}">Reddedildi</option>

                                    @endif

                                    <option value="PENDING">Bekliyor</option>
                                    <option value="SUCCESS">Tamamlandı</option>
                                    <option value="REJECTED">Reddedildi</option>

                                </select>


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
