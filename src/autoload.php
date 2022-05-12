<?php

spl_autoload_register(function ($class) {
    $prefix = 'Leeto\\MoonShine\\';

    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }

    $relativeClass = substr($class, $length);


    $file = rtrim(dirname(__FILE__), '/') . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});