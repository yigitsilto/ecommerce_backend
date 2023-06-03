@extends('admin::layout')

@section('title', 'Bayiler')

@section('content_header')
    <h3>Bayiler/Üye Grupları</h3>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('setting::settings.settings') }}</li>
    </ol>
@endsection

@section('content')

    @component('admin::components.page.index_table')
        @slot('resource', 'company')
        @slot('buttons', ['create'])
        @slot('name', 'Bayi')

        @slot('thead')
            <tr>

                <th>Ad</th>
                <th data-sort>Fiyat Türü</th>
                <th></th>
            </tr>
        @endslot

        @slot('slot')
            @foreach($company as $item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td>{{$item->company_price_id}}</td>
                    <td>
                        <a href="{{route('admin.company.edit',$item->id)}}" class="btn btn-primary">Düzenle</a>
                    </td>
                </tr>

            @endforeach

        @endslot
        @slot('tfoot')
            {{$company->links()}}
        @endslot

    @endcomponent

@endsection





