<?php

spl_autoload_register(static function ($class): void {
    $prefix = 'MoonShine\\';

    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }

    $relativeClass = substr($class, $length);


    $file = rtrim(__DIR__, '/') . '/' . str_replace(
            '\\',
            '/',
            $relativeClass
        ) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
