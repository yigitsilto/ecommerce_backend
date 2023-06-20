<div class="row">
    <div class="col-md-8">
        {{ Form::wysiwyg('translatable[storefront_welcome_text]', trans('storefront::attributes.storefront_welcome_text'), $errors, $settings) }}
        {{ Form::select('storefront_theme_color', 'Buton Rengi', $errors, trans('storefront::themes'), $settings) }}

        <div class="{{ old('storefront_theme_color', array_get($settings, 'storefront_theme_color')) === 'custom_color' ? '' : 'hide' }}"
             id="custom-theme-color">
            {{ Form::color('storefront_custom_theme_color', 'RGB', $errors, $settings) }}
        </div>

        {{ Form::select('storefront_mail_theme_color', 'Yazı Rengi', $errors, trans('storefront::themes'), $settings) }}

        <div class="{{ old('storefront_mail_theme_color', array_get($settings, 'storefront_mail_theme_color')) === 'custom_color' ? '' : 'hide' }}"
             id="custom-mail-theme-color">
            {{ Form::color('storefront_custom_mail_theme_color', 'RGB', $errors, $settings) }}
        </div>

        {{--        {{ Form::select('storefront_slider', trans('storefront::attributes.storefront_slider'), $errors, $sliders, $settings) }}--}}
        {{--        {{ Form::select('storefront_terms_page', trans('storefront::attributes.storefront_terms_page'), $errors, $pages, $settings) }}--}}
        {{--        {{ Form::select('storefront_privacy_page', trans('storefront::attributes.storefront_privacy_page'), $errors, $pages, $settings) }}--}}
        {{ Form::text('translatable[storefront_address]', trans('storefront::attributes.storefront_address'), $errors, $settings) }}

        {{ Form::text('popular_products_text', 'Anasayfa Popüler Ürünler Yazısı', $errors, $settings) }}

        {{ Form::text('popular_categories_text', 'Anasayfa Popüler Kategoriler Yazısı', $errors, $settings) }}

        {{ Form::text('related_products_text', 'Benzer Ürünler Yazısı', $errors, $settings) }}

        {{ Form::text('blog_text', 'Anasayfa Blog Yazısı', $errors, $settings) }}


    </div>
</div>
