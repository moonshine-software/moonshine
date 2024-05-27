<?php

namespace MoonShine\Core\Exceptions;

final class PageException extends MoonShineException
{
    public static function required(): self
    {
        return new self('Page is required');
    }
}
