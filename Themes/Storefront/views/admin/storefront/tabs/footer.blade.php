<div class="row">
    <div class="col-md-8">
        <div class="box-content clearfix">
{{--        	{{ Form::select('storefront_footer_tags', trans('storefront::attributes.storefront_footer_tags'), $errors, $tags, $settings, ['class' => 'selectize prevent-creation', 'multiple' => true]) }}--}}
        	{{ Form::text('translatable[storefront_copyright_text]', trans('storefront::attributes.storefront_copyright_text'), $errors, $settings) }}
        </div>
    </div>
</div>
