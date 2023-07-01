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

            return app('setting');
        }

        if (is_array($key)) {
            return app('setting')->set($key);
        }


        if (!isset($settings[$key])) {
            return app('setting')->get($key, $default);
        }
        return $settings[$key];

    }
}
