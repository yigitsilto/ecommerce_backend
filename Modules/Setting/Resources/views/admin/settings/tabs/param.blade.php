<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('param_enabled', 'Param Durum', 'Param Pos ile ödemeyi aktif hale getir', $errors, $settings) }}
        {{ Form::text('translatable[param_label]', 'Başlık', $errors, $settings, ['required' => true]) }}
        {{ Form::textarea('translatable[param_description]', 'Açıklama', $errors, $settings, ['rows' => 3, 'required' => true]) }}
    </div>
</div>
