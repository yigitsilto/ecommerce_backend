@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('report::admin.reports'))

    <li class="active">{{ trans('report::admin.reports') }}</li>
@endcomponent

@section('content')
    <div class="box box-primary report-wrapper">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-9 col-md-8">
                    <div class="report-result">
                        @yield('report_result')
                    </div>
                </div>

                <div class="col-lg-3 col-md-4">
                    <div class="filter-report clearfix">
                        <h3 class="tab-content-title">{{ trans('report::admin.filter') }}</h3>

                        <form method="GET" action="{{ route('admin.reports.index') }}">
                            <div class="form-group">
                                <label for="report-type">{{ trans('report::admin.filters.report_type') }}</label>

                                <select name="type" id="report-type" class="custom-select-black">
                                    @foreach (trans('report::admin.filters.report_types') as $type => $label)
                                        @if($type == "search_report" || $type == "tax_report" || $type == 'shipping_report' || $type == "taxed_products_report" || $type == "products_view_report")

                                        @else
                                            <option value="{{ $type }}" {{ $request->type === $type ? 'selected' : '' }}>
                                                {{$label}}
                                            </option>

                                        @endif

                                    @endforeach
                                </select>
                            </div>

                            @yield('filters')

                            <button type="submit" class="btn btn-default" data-loading>
                                {{ trans('report::admin.filter') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
