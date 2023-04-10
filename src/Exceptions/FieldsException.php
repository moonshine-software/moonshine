<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;

final class FieldsException extends Exception
{
    public static function wrapError(): self
    {
        return new self("Only FieldsDecoration allowed");
    }
}
