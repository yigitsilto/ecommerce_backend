
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


    @component('admin::components.page.index_table')
        @slot('resource', 'settings.companies')
        @slot('buttons', ['create'])
        @slot('name', 'Kargo Firması Ekle')

        @slot('thead')
            <tr>

                <th>Kargo Firma adı</th>
                <th>Kargo Firma Fiyatı</th>
                <th></th>
            </tr>
        @endslot

        @slot('slot')
            @foreach($company as $item)

                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->price->amount}}</td>

                    <td>
                        <form action="{{route('admin.settings.deleteCompany',$item->id)}}" method="POST">
                            {{ csrf_field() }}

                            <button class="btn btn-danger" type="submit">Sil</button>
                        </form>

                    </td>
                </tr>
            @endforeach
        @endslot
    @endcomponent

@endsection





