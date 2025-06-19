<?php

spl_autoload_register(function ($c) {
    $relative = str_replace('\\', '/', $c) . '.php';
    foreach ([__DIR__, dirname(__DIR__) . '/src'] as $base) {
        $path = $base . '/' . $relative;
        if (is_file($path)) {
            require $path;
            return;
        }
    }
});
