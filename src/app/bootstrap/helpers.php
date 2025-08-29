<?php

if (!function_exists('trans')) {

    // Хелпер переводов
    function trans($key, $replace = [])
    {
        global $app;

        return $app['translator']->get($key, $replace);
    }
}
