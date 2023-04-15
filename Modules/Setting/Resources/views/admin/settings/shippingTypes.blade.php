
@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <h3>Kargo Firmaları</h3>

    <a href="{{ route('admin.settings.companies.create') }}" style="text-align:right" class="btn btn-primary">Firma Ekle</a>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('setting::settings.settings') }}</li>
    </ol>
@endsection

@section('content')
  <div class="card">




          <div class="row">

              <div class="col-md-12">
                  <table class="table table-bordered">
                      <thead>
                      <tr>
                          <td>Kargo Firma adı</td>
                          <td>Kargo Firma Fiyatı</td>
                          <td></td>
                      </tr>
                      </thead>

                      <tbody>
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
                      </tbody>
                  </table>

              </div>
          </div>


  </div>
@endsection





