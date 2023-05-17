@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', "Filtreler")

@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'filters')
    @slot('name', "Filtre")

    @slot('thead')
        <tr>

            <th>Başlık</th>
            <th>Tarih</th>
            <th></th>
            <th></th>
        </tr>
    @endslot
    @slot('slot')
        @foreach($filters as $item)
            <tr>
                <td>{{$item->title}}</td>
                <td>{{$item->created_at}}</td>
                <td>
                    <a href="{{route('admin.filters.edit',$item->id)}}" class="btn btn-primary">Düzenle</a>
                </td>
                <td>
                    <form action="{{route('admin.filters.delete',$item->id)}}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE')}}

                       @if($item->status)

                            <button class="btn btn-danger" type="submit">Pasif Yap</button>

                        @else

                            <button class="btn btn-success" type="submit">Aktif Yap</button>
                       @endif
                    </form>
                </td>
            </tr>

        @endforeach

    @endslot
    @slot('tfoot')
        {{$filters->links()}}
    @endslot

@endcomponent

