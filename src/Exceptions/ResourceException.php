<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class ResourceException extends Exception
{
    public static function queryError(string $message): self
    {
        return new self("Query error: $message");
    }

    public static function withoutSoftDeletes(): self
    {
        return new self("Resource model does not have soft deletes");
    }
}
