<div class="row">
    <div class="col-md-8">
        {{--        {{ Form::number('price', trans('product::attributes.price'), $errors, $product, ['min' => 0, 'required' => true]) }}--}}
        {{ Form::number('special_price', trans('product::attributes.special_price'), $errors, $product, ['min' => 0]) }}
        {{ Form::select('special_price_type', trans('product::attributes.special_price_type'), $errors, [
    'fixed' => 'Sabit'
], $product) }}

        @foreach($companyPrices as $index => $item)
            @if($index == 0)
                <label for="{{$index}}">{{$item->title}} <span style="color: red"> *</span> </label>
                <input id="{{$index}}" type="number" class="form-control" name="prices[{{$item->id}}]" value="{{$product->productPrices[0]->price}}" required>
                @else

                <label for="{{$index}}">{{$item->title}}</label>
                <input type="number" id="{{$index}}" class="form-control" name="prices[{{$item->id}}]" value="{{$product->productPrices->has($index) ? $product->productPrices[$index]->price : 0}}">

            @endif


        @endforeach

        {{--        {{ Form::text('special_price_start', trans('product::attributes.special_price_start'), $errors, $product, ['class' => 'datetime-picker']) }}--}}
        {{--        {{ Form::text('special_price_end', trans('product::attributes.special_price_end'), $errors, $product, ['class' => 'datetime-picker']) }}--}}
    </div>
</div>
