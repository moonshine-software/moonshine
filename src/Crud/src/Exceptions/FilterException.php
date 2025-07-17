<?php

declare(strict_types=1);

namespace MoonShine\Crud\Exceptions;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\MoonShineException;

final class FilterException extends MoonShineException
{
    /**
     * @param class-string<FieldContract> $fieldClass
     */
    public static function notAcceptable(string $fieldClass): self
    {
        return new self("You can't use $fieldClass inside filters.");
    }
}
