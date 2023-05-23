@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>İade</h3>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('setting::settings.settings') }}</li>
    </ol>
@endsection

@section('content')

    @component('admin::components.page.index_table')

        @slot('thead')
            <tr>

                <th>Ürün</th>
                <th>Kullanıcı</th>
                <th>Durum</th>
                <th data-sort>Tarih</th>
                <th></th>
            </tr>
        @endslot

        @slot('slot')
            @foreach($refunds as $item)
                <tr>
                    <td>{{$item->product->name}}</td>
                    <td>{{$item->user->first_name}} {{$item->user->last_name}}</td>
                    <td>

                        @if($item->status == 'PENDING')
                            <span class="badge badge-warning">Bekliyor</span>

                        @elseif($item->status == 'SUCCESS')
                            <span class="bade badge-success">Tamamlandı</span>

                        @elseif($item->status == 'REJECTED')

                            <span class="badge badge-danger">Reddedildi</span>

                        @endif


                    </td>
                    <td>{{$item->created_at}}</td>
                    <td><a href="{{route('admin.refunds.show',$item->id)}}" class="btn btn-primary btn-sm">Görüntüle</a>
                    </td>
                </tr>

            @endforeach

        @endslot
        @slot('tfoot')
            {{$refunds->links()}}
        @endslot

    @endcomponent

@endsection





