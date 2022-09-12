<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class FieldsException extends Exception
{
    public static function wrapError(): self
    {
        return new static("Only FieldsDecoration allowed");
    }
}
