<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class ViewException extends Exception
{
    public static function notFound(): self
    {
        return new static('View not found');
    }
}
