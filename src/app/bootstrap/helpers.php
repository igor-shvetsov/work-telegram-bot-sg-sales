<?php

// Хелпер переводов
function trans($key, $replace = []) {
    global $app;

    return $app['translator']->get($key, $replace);
}