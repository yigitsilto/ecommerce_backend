@extends('admin::layout')

@section('title', 'Filtre Ekle')

@section('content_header')
    <h3>Filtre</h3>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">Filtreler</li>
    </ol>
@endsection

@section('content')
    <div class="box box-default">
        <div class="box-body clearfix">

            <div class="col-lg-12 col-md-12">
                <div class="tab-wrapper category-details-tab">

                    <form method="POST" action="{{ route('admin.filters.update',$filter->id) }}" class="form-horizontal"
                          id="create-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="tab-content">
                            <div id="general-information" class="tab-pane fade in active">

                                {{ Form::text('title', 'Başlık', $errors, $filter, ['labelCol' => 2, 'required' => true]) }}

                                <!-- Multiple text value input triggered by plus button -->

                                <div id="valuesContainer">
                                   @foreach($filter->values as $key => $value)

                                        <div style="display: flex; flex:1; align-items: center; ">
                                            <div class="col-12" style="width: 100%">
                                                <label for="">Filtre Değerleri *</label>
                                                <input class="form-control" type="text" required name="values[{{$key}}][title]" placeholder="Yazınız..."
                                                       value="{{$value->title}}">

                                                <input class="form-control" type="hidden" required name="values[{{$key}}][id]" placeholder="Yazınız..."
                                                       value="{{$value->id}}">
                                            </div>

                                           @if($key == 0)
                                                <div>
                                                    <button type="button" class="btn btn-primary" style="margin-top: 24px"
                                                            onclick="addNewValue()">
                                                        +
                                                    </button>
                                                </div>
                                           @endif



                                        </div>


                                   @endforeach
                                </div>

                                <button type="submit" class="btn btn-primary mt-4" style="margin-top: 12px"
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

    <script>
        function addNewValue() {
            var container = document.createElement("div");
            container.style.display = "flex";
            container.style.flex = "1";
            container.style.alignItems = "center";

            var inputDiv = document.createElement("div");
            inputDiv.className = "col-12";
            inputDiv.style.width = "100%";

            var input = document.createElement("input");
            input.type = "text";
            input.required = true;
            input.style.marginTop = "24px";
            input.className = "form-control";
            input.name = "newValues[]";
            input.value = "";
            input.placeholder = "Yazınız...";
            inputDiv.appendChild(input);


            container.appendChild(inputDiv);

            var valuesContainer = document.getElementById("valuesContainer");
            valuesContainer.appendChild(container);
        }
    </script>

@endsection
