<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('ziraat_enabled', 'Param Durum', 'Ziraat ile ödemeyi aktif hale getir', $errors, $settings) }}
        {{ Form::text('translatable[ziraat_label]', 'Başlık', $errors, $settings, ['required' => true]) }}
        {{ Form::textarea('translatable[ziraat_description]', 'Açıklama', $errors, $settings, ['rows' => 3, 'required' => true]) }}
    </div>
</div>
