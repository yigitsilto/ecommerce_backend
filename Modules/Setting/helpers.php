<?php

if (!function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed, we'll assume you want to set settings.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed|\Modules\Setting\Repository
     */
    function setting($key = null, $default = null)
    {
        // TODO buraya bakılacak bütün veriler redise eklenmeli oradan çekilmeli yoksa tenant yapıda sıkıntı olabilir
        if (is_null($key)) {
            $settings = \Illuminate\Support\Facades\Redis::get('general_settings');
            $settings = unserialize($settings);
            return app('setting');
        }

        if (is_array($key)) {
            $settings = \Illuminate\Support\Facades\Redis::set('general_settings', serialize($key));
            return app('setting')->set($key);
        }

        try {
            $settings = \Illuminate\Support\Facades\Redis::get('general_settings');
            $settings = unserialize($settings);


            if (!isset($settings[$key])) {
                return app('setting')->get($key, $default);
            }
            return $settings[$key];
        } catch (PDOException $e) {
            return $default;
        }
    }
}
