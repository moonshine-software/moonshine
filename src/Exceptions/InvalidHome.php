<?php

namespace MoonShine\Exceptions;

use Exception;

class InvalidHome extends Exception
{
    public static function create(string $class): self
    {
        return new self("Could not create the home, `$class` does not implement `Page` or `Resource`");
    }
}
