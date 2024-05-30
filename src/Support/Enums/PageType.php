<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum PageType: string
{
    case  INDEX = 'index-page';

    case  FORM = 'form-page';

    case  DETAIL = 'detail-page';

    public static function getTypeFromUri(string $uri): ?self
    {
        return match ($uri) {
            'index-page' => self::INDEX,
            'form-page' => self::FORM,
            'detail-page' => self::DETAIL,
            default => null
        };
    }
}
