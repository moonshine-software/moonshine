<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class ViewComponentException extends Exception
{
    public static function notFoundInView(string $viewClass): self
    {
        return new static("ViewComponent not found in $viewClass");
    }
}
