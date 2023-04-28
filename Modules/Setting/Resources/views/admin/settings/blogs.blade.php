@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>Blog</h3>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('setting::settings.settings') }}</li>
    </ol>
@endsection

@section('content')

    @component('admin::components.page.index_table')
        @slot('resource', 'blogs')
        @slot('buttons', ['create'])
        @slot('name', 'Blog Ekle')

        @slot('thead')
            <tr>

                <th>Başlık</th>
                <th data-sort>Tarih</th>
                <th></th>
                <th></th>
            </tr>
        @endslot

        @slot('slot')
            @foreach($blog as $item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td>{{$item->created_at}}</td>
                    <td>
                        <a href="{{route('admin.blogs.edit',$item->id)}}" class="btn btn-primary">Düzenle</a>
                    </td>
                    <td>
                        <form action="{{route('admin.blogs.delete',$item->id)}}" method="POST">
                            {{ csrf_field() }}

                            <button class="btn btn-danger" type="submit">Sil</button>
                        </form>
                    </td>
                </tr>

            @endforeach

        @endslot
        @slot('tfoot')
            {{$blog->links()}}
        @endslot

    @endcomponent

@endsection





