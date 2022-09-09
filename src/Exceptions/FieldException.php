<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class FieldException extends Exception
{
    public static function notAllowedFileExtension(string $extension): self
    {
        return new static("$extension not allowed");
    }
}
